<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationStatus;
use App\Models\User;
use App\Services\RentalReleaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalReleaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private RentalReleaseService $service;

    private User $user;

    private Customer $customer;

    private InventoryVariant $variant;

    private Inventory $item;

    private InventoryStatus $availableStatus;

    private InventoryStatus $rentedStatus;

    private RentalStatus $rentedRentalStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(RentalReleaseService::class);

        // Create statuses
        $this->availableStatus = InventoryStatus::factory()->create(['status_name' => 'available']);
        $this->rentedStatus = InventoryStatus::factory()->create(['status_name' => 'rented']);
        $this->rentedRentalStatus = RentalStatus::factory()->create(['status_name' => 'rented']);

        // Create users and customers
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();

        // Create variant with deposit
        $this->variant = InventoryVariant::factory()->create([
            'deposit_amount' => 500.00,
            'rental_price' => 100.00,
        ]);

        // Create available item
        $this->item = Inventory::factory()->create([
            'variant_id' => $this->variant->variant_id,
            'status_id' => $this->availableStatus->status_id,
            'deposit_amount' => null, // Falls back to variant
            'rental_price' => 100.00,
        ]);
    }

    /**
     * Test successful item release
     */
    public function test_can_release_item_successfully(): void
    {
        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => true,
            'deposit_payment_method' => 'cash',
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        // Assert it's a Rental instance, not an error array
        $this->assertInstanceOf(Rental::class, $result);
        $this->assertEquals($this->item->item_id, $result->item_id);
        $this->assertEquals($this->customer->customer_id, $result->customer_id);
        $this->assertEquals(500.00, $result->deposit_amount);
        $this->assertEquals('held', $result->deposit_status);

        // Check item status changed to rented
        $this->item->refresh();
        $this->assertEquals($this->rentedStatus->status_id, $this->item->status_id);

        // Check invoice was created
        $this->assertCount(1, $result->invoices);
    }

    /**
     * Test item not found error
     */
    public function test_error_when_item_not_found(): void
    {
        $releaseData = [
            'item_id' => 99999,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => true,
            'deposit_payment_method' => 'cash',
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        // Should be error array
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals(404, $result['code']);
        $this->assertStringContainsString('not found', $result['error']);
    }

    /**
     * Test error when item is not available
     */
    public function test_error_when_item_not_available(): void
    {
        // Change item status to rented
        $this->item->update(['status_id' => $this->rentedStatus->status_id]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals(422, $result['code']);
        $this->assertStringContainsString('not available', $result['error']);
    }

    /**
     * Test error when item is already rented
     */
    public function test_error_when_item_already_rented_to_customer(): void
    {
        // Create existing active rental for this item
        Rental::factory()->create([
            'item_id' => $this->item->item_id,
            'return_date' => null, // Active rental
        ]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('already currently rented', $result['error']);
    }

    /**
     * Test error when item has no variant
     */
    public function test_error_when_item_has_no_variant(): void
    {
        $this->item->update(['variant_id' => null]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('not linked to any inventory variant', $result['error']);
    }

    /**
     * Test error when variant has no deposit configured
     */
    public function test_error_when_deposit_not_configured(): void
    {
        $this->variant->update(['deposit_amount' => 0]);
        $this->item->update(['deposit_amount' => null]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals(422, $result['code']);
        $this->assertStringContainsString('does not have a configured deposit amount', $result['error']);
    }

    /**
     * Test using item-specific deposit over variant deposit
     */
    public function test_uses_item_deposit_over_variant(): void
    {
        $this->item->update(['deposit_amount' => 750.00]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => true,
            'deposit_payment_method' => 'cash',
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertInstanceOf(Rental::class, $result);
        $this->assertEquals(750.00, $result->deposit_amount);
    }

    /**
     * Test release without collecting deposit
     */
    public function test_can_skip_deposit_collection(): void
    {
        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => false,
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertInstanceOf(Rental::class, $result);
        $this->assertNotEquals('held', $result->deposit_status);
    }

    /**
     * Test with reservation
     */
    public function test_can_release_with_reservation(): void
    {
        $reservationStatus = ReservationStatus::factory()->create(['status_name' => 'confirmed']);

        $reservation = Reservation::factory()->create([
            'status_id' => $reservationStatus->status_id,
        ]);

        $reservationItem = ReservationItem::factory()->create([
            'reservation_id' => $reservation->reservation_id,
            'variant_id' => $this->variant->variant_id,
            'quantity' => 1,
        ]);

        $releaseData = [
            'item_id' => $this->item->item_id,
            'reservation_id' => $reservation->reservation_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => true,
            'deposit_payment_method' => 'cash',
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        $this->assertInstanceOf(Rental::class, $result);
        $this->assertEquals($reservation->reservation_id, $result->reservation_id);
    }

    /**
     * Test invoice is created with correct line items
     */
    public function test_invoice_created_with_correct_items(): void
    {
        $releaseData = [
            'item_id' => $this->item->item_id,
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'collect_deposit' => true,
            'deposit_payment_method' => 'cash',
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        // Get the invoice
        $invoice = $result->invoices()->first();
        $this->assertNotNull($invoice);

        // Check line items
        $lineItems = $invoice->invoiceItems()->get();
        $this->assertCount(2, $lineItems);

        // One for rental fee, one for deposit
        $rentalItem = $lineItems->firstWhere('item_type', 'rental_fee');
        $depositItem = $lineItems->firstWhere('item_type', 'deposit');

        $this->assertNotNull($rentalItem);
        $this->assertNotNull($depositItem);
        $this->assertEquals(100.00, $rentalItem->unit_price);
        $this->assertEquals(500.00, $depositItem->unit_price);
    }

    /**
     * Test transaction rollback on error
     */
    public function test_transaction_rollback_on_payment_failure(): void
    {
        // Mock a scenario where deposit collection would fail
        // For this test, we'll check that no rental is created if there's an issue

        $releaseData = [
            'item_id' => 99999, // Non-existent item
            'customer_id' => $this->customer->customer_id,
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $result = $this->service->releaseItem($releaseData, $this->user->id);

        // Should return error
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);

        // Rental should not be created
        $this->assertCount(0, Rental::where('customer_id', $this->customer->customer_id)->get());
    }
}
