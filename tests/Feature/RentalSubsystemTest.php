<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use App\Models\Invoice;
use App\Models\PaymentStatus;
use App\Models\Rental;
use App\Models\RentalExtension;
use App\Models\RentalSetting;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\User;
use App\Services\DepositService;
use App\Services\RentalReleaseService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RentalSubsystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $clerk;

    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->clerk = User::factory()->create(['is_admin' => false]);
    }

    // -------------------------------------------------------------------------
    // Status seed helpers – call only the ones each test needs
    // -------------------------------------------------------------------------

    private function activeRentalStatus(): RentalStatus
    {
        return RentalStatus::factory()->create(['status_name' => 'active']);
    }

    private function returnedRentalStatus(): RentalStatus
    {
        return RentalStatus::factory()->create(['status_name' => 'returned']);
    }

    private function cancelledRentalStatus(): RentalStatus
    {
        return RentalStatus::factory()->create(['status_name' => 'cancelled']);
    }

    private function overdueRentalStatus(): RentalStatus
    {
        return RentalStatus::factory()->create(['status_name' => 'Overdue']);
    }

    private function availableInventoryStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'available']);
    }

    private function rentedInventoryStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'rented']);
    }

    private function unpaidPaymentStatus(): PaymentStatus
    {
        return PaymentStatus::factory()->create(['status_name' => 'unpaid']);
    }

    private function paidPaymentStatus(): PaymentStatus
    {
        return PaymentStatus::factory()->create(['status_name' => 'paid']);
    }

    // -------------------------------------------------------------------------
    // Domain object helpers
    // -------------------------------------------------------------------------

    private function makeCustomer(): Customer
    {
        $customerStatus = CustomerStatus::factory()->create(['status_name' => 'active']);
        return Customer::factory()->create(['status_id' => $customerStatus->status_id]);
    }

    private function makeInventoryItem(InventoryStatus $status): Inventory
    {
        $variant = InventoryVariant::factory()->create([
            'total_units'     => 1,
            'available_units' => 1,
        ]);
        return Inventory::factory()->create([
            'variant_id' => $variant->variant_id,
            'status_id'  => $status->status_id,
        ]);
    }

    /**
     * Create a confirmed reservation so that index() / report() queries
     * (which filter by reservation.status = 'confirmed') can find the rentals.
     */
    private function makeConfirmedReservation(Customer $customer): Reservation
    {
        $confirmedReservationStatus = ReservationStatus::factory()->create(['status_name' => 'confirmed']);
        return Reservation::factory()->create([
            'customer_id' => $customer->customer_id,
            'status_id'   => $confirmedReservationStatus->status_id,
        ]);
    }

    /**
     * Create an active (not returned) rental with a confirmed reservation.
     */
    private function makeActiveRental(
        Customer $customer,
        Inventory $item,
        RentalStatus $rentalStatus,
        array $overrides = []
    ): Rental {
        $reservation = $this->makeConfirmedReservation($customer);

        return Rental::factory()->create(array_merge([
            'customer_id'    => $customer->customer_id,
            'item_id'        => $item->item_id,
            'status_id'      => $rentalStatus->status_id,
            'reservation_id' => $reservation->reservation_id,
            'released_date'  => now()->subDays(3)->format('Y-m-d'),
            'due_date'       => now()->addDays(4)->format('Y-m-d'),
            'return_date'    => null,
        ], $overrides));
    }

    // =========================================================================
    // WBT_RNT_001 – releaseItem() : Success path
    // =========================================================================

    /**
     * WBT_RNT_001: releaseItem() - HTTP 201; message 'Item released successfully to customer'.
     */
    public function test_release_item_success_path(): void
    {
        // Mock RentalReleaseService to return a successful result array
        $mockResult = ['rental_id' => 1, 'status' => 'released'];

        $this->mock(RentalReleaseService::class, function ($mock) use ($mockResult) {
            $mock->shouldReceive('releaseItem')
                 ->once()
                 ->andReturn($mockResult);
        });

        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $reservation     = $this->makeConfirmedReservation($customer);
        $activeStatus    = $this->activeRentalStatus();

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/release', [
                'reservation_id' => $reservation->reservation_id,
                'item_id'        => $item->item_id,
                'customer_id'    => $customer->customer_id,
                'released_date'  => now()->format('Y-m-d'),
                'due_date'       => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Item released successfully to customer');
    }

    // =========================================================================
    // WBT_RNT_002 – releaseItem() : Service returns error array
    // =========================================================================

    /**
     * WBT_RNT_002: releaseItem() - HTTP 422 when service returns an error array.
     */
    public function test_release_item_service_returns_error_array(): void
    {
        $this->mock(RentalReleaseService::class, function ($mock) {
            $mock->shouldReceive('releaseItem')
                 ->once()
                 ->andReturn(['error' => 'Item not available', 'code' => 422]);
        });

        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $reservation     = $this->makeConfirmedReservation($customer);
        $this->activeRentalStatus();

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/release', [
                'reservation_id' => $reservation->reservation_id,
                'item_id'        => $item->item_id,
                'customer_id'    => $customer->customer_id,
                'released_date'  => now()->format('Y-m-d'),
                'due_date'       => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Item not available');
    }

    // =========================================================================
    // WBT_RNT_003 – releaseItem() : Unhandled exception
    // =========================================================================

    /**
     * WBT_RNT_003: releaseItem() - HTTP 500 when service throws an Exception;
     * response includes 'error' and 'trace'.
     */
    public function test_release_item_unhandled_exception(): void
    {
        $this->mock(RentalReleaseService::class, function ($mock) {
            $mock->shouldReceive('releaseItem')
                 ->once()
                 ->andThrow(new \Exception('Unexpected DB error'));
        });

        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $reservation     = $this->makeConfirmedReservation($customer);
        $this->activeRentalStatus();

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/release', [
                'reservation_id' => $reservation->reservation_id,
                'item_id'        => $item->item_id,
                'customer_id'    => $customer->customer_id,
                'released_date'  => now()->format('Y-m-d'),
                'due_date'       => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(500)
                 ->assertJsonPath('message', 'Failed to process return')
                 ->assertJsonStructure(['error', 'trace']);
    }

    // =========================================================================
    // WBT_RNT_004 – processReturn() : Validation failure
    // =========================================================================

    /**
     * WBT_RNT_004: processReturn() - HTTP 422 with 'Validation failed' when
     * return_date is missing.
     */
    public function test_process_return_validation_failure(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                // return_date intentionally omitted
                'return_notes' => 'Returned in good condition',
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Validation failed')
                 ->assertJsonStructure(['errors']);
    }

    // =========================================================================
    // WBT_RNT_005 – processReturn() : Already returned
    // =========================================================================

    /**
     * WBT_RNT_005: processReturn() - HTTP 422 when rental already has a return_date.
     */
    public function test_process_return_already_returned(): void
    {
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $returnedStatus, [
            'return_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'This rental has already been returned.');
    }

    // =========================================================================
    // WBT_RNT_006 – processReturn() : Missing inventory statuses
    // =========================================================================

    /**
     * WBT_RNT_006: processReturn() - HTTP 500 when 'available' or 'rented'
     * InventoryStatus records are absent from DB.
     */
    public function test_process_return_missing_inventory_statuses(): void
    {
        $activeStatus = $this->activeRentalStatus();
        // Create a rented inventory status for the item but NOT 'available'
        $rentedStatus = $this->rentedInventoryStatus();
        $customer     = $this->makeCustomer();
        $item         = $this->makeInventoryItem($rentedStatus);
        $rental       = $this->makeActiveRental($customer, $item, $activeStatus);

        // Ensure neither 'available' nor another needed status exists
        InventoryStatus::where('status_name', 'available')->delete();

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(500)
                 ->assertJsonPath('message', 'Required inventory statuses (available/rented) are missing.');
    }

    // =========================================================================
    // WBT_RNT_007 – processReturn() : Full deposit return
    // =========================================================================

    /**
     * WBT_RNT_007: processReturn() - DepositService::returnFullDeposit called;
     * rental status set to 'returned'; item status set to 'available'; HTTP 200.
     */
    public function test_process_return_full_deposit_return(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $rentedStatus    = $this->rentedInventoryStatus();

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($rentedStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'deposit_amount' => 500,
            'deposit_status' => 'held',
        ]);

        $this->mock(DepositService::class, function ($mock) {
            $mock->shouldReceive('returnFullDeposit')->once();
        });

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date'          => now()->format('Y-m-d'),
                'deposit_return_action' => 'full',
                'deposit_return_method' => 'cash',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental return processed successfully');

        $this->assertDatabaseHas('rentals', [
            'rental_id'   => $rental->rental_id,
            'status_id'   => $returnedStatus->status_id,
        ]);

        $this->assertDatabaseHas('inventories', [
            'item_id'   => $item->item_id,
            'status_id' => $availableStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_008 – processReturn() : Partial deposit return
    // =========================================================================

    /**
     * WBT_RNT_008: processReturn() - DepositService::returnPartialDeposit called;
     * HTTP 200.
     */
    public function test_process_return_partial_deposit_return(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $rentedStatus    = $this->rentedInventoryStatus();

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($rentedStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'deposit_amount' => 500,
            'deposit_status' => 'held',
        ]);

        $this->mock(DepositService::class, function ($mock) {
            $mock->shouldReceive('returnPartialDeposit')->once();
        });

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date'           => now()->format('Y-m-d'),
                'deposit_return_action' => 'partial',
                'deposit_return_method' => 'cash',
                'deductions'            => [
                    ['type' => 'damage', 'amount' => 100, 'reason' => 'Stain on fabric'],
                ],
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental return processed successfully');
    }

    // =========================================================================
    // WBT_RNT_009 – processReturn() : Deposit forfeit
    // =========================================================================

    /**
     * WBT_RNT_009: processReturn() - DepositService::forfeitDeposit called;
     * rental returned; item set to available; HTTP 200.
     */
    public function test_process_return_deposit_forfeit(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $rentedStatus    = $this->rentedInventoryStatus();

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($rentedStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'deposit_amount' => 500,
            'deposit_status' => 'held',
        ]);

        $this->mock(DepositService::class, function ($mock) {
            $mock->shouldReceive('forfeitDeposit')->once();
        });

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date'           => now()->format('Y-m-d'),
                'deposit_return_action' => 'forfeit',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental return processed successfully');

        $this->assertDatabaseHas('inventories', [
            'item_id'   => $item->item_id,
            'status_id' => $availableStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_010 – processReturn() : Default hold (no deposit action)
    // =========================================================================

    /**
     * WBT_RNT_010: processReturn() - No deposit service method called when
     * deposit_return_action defaults to 'hold'; rental updated; item available; HTTP 200.
     */
    public function test_process_return_default_hold_no_deposit_action(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $rentedStatus    = $this->rentedInventoryStatus();

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($rentedStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus);

        // DepositService must not have any of its deposit methods called
        $this->mock(DepositService::class, function ($mock) {
            $mock->shouldNotReceive('returnFullDeposit');
            $mock->shouldNotReceive('returnPartialDeposit');
            $mock->shouldNotReceive('forfeitDeposit');
        });

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/return", [
                'return_date' => now()->format('Y-m-d'),
                // deposit_return_action omitted → defaults to 'hold'
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental return processed successfully');

        $this->assertDatabaseHas('inventories', [
            'item_id'   => $item->item_id,
            'status_id' => $availableStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_011 – extendRental() : Already returned
    // =========================================================================

    /**
     * WBT_RNT_011: extendRental() - HTTP 422 when rental already has a return_date.
     */
    public function test_extend_rental_already_returned(): void
    {
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $returnedStatus, [
            'return_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/extend", [
                'new_due_date' => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot extend a rental that has already been returned.');
    }

    // =========================================================================
    // WBT_RNT_012 – extendRental() : Overdue rental
    // =========================================================================

    /**
     * WBT_RNT_012: extendRental() - HTTP 422 when rental is overdue.
     */
    public function test_extend_rental_overdue_rental(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => now()->subDays(2)->format('Y-m-d'), // Already past due
            'return_date' => null,
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/extend", [
                'new_due_date' => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot extend an overdue rental. Please settle penalties first.');
    }

    // =========================================================================
    // WBT_RNT_013 – extendRental() : Successful extension
    // =========================================================================

    /**
     * WBT_RNT_013: extendRental() - RentalExtension created; due_date updated;
     * extension_count incremented; HTTP 200.
     */
    public function test_extend_rental_successful_extension(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'        => now()->addDays(4)->format('Y-m-d'),
            'extension_count' => 0,
        ]);

        $newDueDate = now()->addDays(7)->format('Y-m-d');

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/extend", [
                'new_due_date'     => $newDueDate,
                'extension_reason' => 'Customer requested more time',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental extended successfully');

        // due_date must be updated
        $this->assertDatabaseHas('rentals', [
            'rental_id'       => $rental->rental_id,
            'due_date'        => $newDueDate,
            'extension_count' => 1,
        ]);

        // RentalExtension record must have been created
        $this->assertDatabaseHas('rental_extensions', [
            'rental_id'   => $rental->rental_id,
            'new_due_date' => $newDueDate,
        ]);
    }

    // =========================================================================
    // WBT_RNT_014 – bulkExtend() : Mixed results
    // =========================================================================

    /**
     * WBT_RNT_014: bulkExtend() - Active rental succeeds; overdue rental fails
     * with penalty message; already-returned rental appears in failed list.
     */
    public function test_bulk_extend_mixed_results(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();

        // Rental 1: Active, not overdue — should succeed
        $item1   = $this->makeInventoryItem($availableStatus);
        $rental1 = $this->makeActiveRental($customer, $item1, $activeStatus, [
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        // Rental 2: Overdue — should fail with penalty message
        $item2   = $this->makeInventoryItem($availableStatus);
        $rental2 = $this->makeActiveRental($customer, $item2, $activeStatus, [
            'due_date'    => now()->subDays(3)->format('Y-m-d'),
            'return_date' => null,
        ]);

        // Rental 3: Already returned — should appear in failed list
        $item3   = $this->makeInventoryItem($availableStatus);
        $rental3 = $this->makeActiveRental($customer, $item3, $returnedStatus, [
            'return_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/bulk-extend', [
                'rental_ids' => [$rental1->rental_id, $rental2->rental_id, $rental3->rental_id],
                'days'       => 5,
                'reason'     => 'Bulk customer extension',
            ]);

        $response->assertStatus(200);

        $results = $response->json('results');

        // Rental 1 must be in the success list
        $this->assertContains($rental1->rental_id, $results['success']);

        // Rental 2 must be in the failed list with an overdue penalty message
        $failedIds   = collect($results['failed'])->pluck('rental_id')->all();
        $failedEntry = collect($results['failed'])->firstWhere('rental_id', $rental2->rental_id);
        $this->assertContains($rental2->rental_id, $failedIds);
        $this->assertStringContainsString('Settle penalties first', $failedEntry['reason']);

        // Rental 3 (already returned) must be in the failed list
        $this->assertContains($rental3->rental_id, $failedIds);
    }

    // =========================================================================
    // WBT_RNT_015 – bulkReturn() : Missing statuses
    // =========================================================================

    /**
     * WBT_RNT_015: bulkReturn() - HTTP 500 when 'available' or 'returned'
     * status is missing from DB.
     */
    public function test_bulk_return_missing_statuses(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        // Intentionally do NOT create 'returned' RentalStatus or 'available' InventoryStatus
        RentalStatus::where('status_name', 'returned')->delete();
        InventoryStatus::where('status_name', 'available')->delete();

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/bulk-return', [
                'rental_ids'  => [$rental->rental_id],
                'return_date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(500)
                 ->assertJsonPath('message', 'Required statuses (available/returned) are missing from the system.');
    }

    // =========================================================================
    // WBT_RNT_016 – bulkReturn() : Successful bulk return
    // =========================================================================

    /**
     * WBT_RNT_016: bulkReturn() - Both rentals updated with return_date; items set
     * to 'available'; InventoryMovement records created; HTTP 200.
     */
    public function test_bulk_return_successful(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $rentedStatus    = $this->rentedInventoryStatus();
        $customer        = $this->makeCustomer();

        $item1   = $this->makeInventoryItem($rentedStatus);
        $item2   = $this->makeInventoryItem($rentedStatus);
        $rental1 = $this->makeActiveRental($customer, $item1, $activeStatus);
        $rental2 = $this->makeActiveRental($customer, $item2, $activeStatus);

        $returnDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->clerk)
            ->postJson('/api/rentals/bulk-return', [
                'rental_ids'  => [$rental1->rental_id, $rental2->rental_id],
                'return_date' => $returnDate,
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('2', $response->json('message'));

        // Both rentals must have return_date and returned status
        foreach ([$rental1, $rental2] as $rental) {
            $this->assertDatabaseHas('rentals', [
                'rental_id'   => $rental->rental_id,
                'return_date' => $returnDate,
                'status_id'   => $returnedStatus->status_id,
            ]);
        }

        // Both items must be back to 'available'
        foreach ([$item1, $item2] as $item) {
            $this->assertDatabaseHas('inventories', [
                'item_id'   => $item->item_id,
                'status_id' => $availableStatus->status_id,
            ]);
        }

        // InventoryMovement records must have been created
        $this->assertDatabaseCount('inventory_movements', 2);
    }

    // =========================================================================
    // WBT_RNT_017 – cancel() : Already returned
    // =========================================================================

    /**
     * WBT_RNT_017: cancel() - HTTP 422 when rental already has a return_date.
     */
    public function test_cancel_already_returned(): void
    {
        $returnedStatus  = $this->returnedRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $returnedStatus, [
            'return_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/cancel");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot cancel a rental that has already been returned.');
    }

    // =========================================================================
    // WBT_RNT_018 – cancel() : Has paid invoices
    // =========================================================================

    /**
     * WBT_RNT_018: cancel() - HTTP 422 when rental has an invoice with balance_due <= 0.
     */
    public function test_cancel_has_paid_invoices(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $paidStatus      = $this->paidPaymentStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        // Invoice with balance_due = 0 (fully paid)
        Invoice::factory()->create([
            'rental_id'   => $rental->rental_id,
            'customer_id' => $customer->customer_id,
            'status_id'   => $paidStatus->status_id,
            'balance_due' => 0,
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/cancel");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot cancel rental. It has paid invoices associated with it.');
    }

    // =========================================================================
    // WBT_RNT_019 – cancel() : Cancelled status not found
    // =========================================================================

    /**
     * WBT_RNT_019: cancel() - HTTP 500 when 'cancelled' RentalStatus doesn't exist.
     */
    public function test_cancel_cancelled_status_not_found(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        // Ensure no 'cancelled' status exists
        RentalStatus::where('status_name', 'cancelled')->delete();

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/cancel");

        $response->assertStatus(500)
                 ->assertJsonPath('message', 'Cancelled status not found in the system.');
    }

    // =========================================================================
    // WBT_RNT_020 – cancel() : Successful cancellation
    // =========================================================================

    /**
     * WBT_RNT_020: cancel() - Rental → 'cancelled'; reservation → 'cancelled';
     * item → 'available'; HTTP 200.
     */
    public function test_cancel_successful_cancellation(): void
    {
        $activeStatus       = $this->activeRentalStatus();
        $cancelledRntStatus = $this->cancelledRentalStatus();
        $availableStatus    = $this->availableInventoryStatus();
        $rentedStatus       = $this->rentedInventoryStatus();

        $cancelledResStatus = ReservationStatus::factory()->create(['status_name' => 'cancelled']);
        $confirmedResStatus = ReservationStatus::factory()->create(['status_name' => 'confirmed']);

        $customer    = $this->makeCustomer();
        $item        = $this->makeInventoryItem($rentedStatus);
        $reservation = Reservation::factory()->create([
            'customer_id' => $customer->customer_id,
            'status_id'   => $confirmedResStatus->status_id,
        ]);

        $rental = Rental::factory()->create([
            'customer_id'    => $customer->customer_id,
            'item_id'        => $item->item_id,
            'status_id'      => $activeStatus->status_id,
            'reservation_id' => $reservation->reservation_id,
            'released_date'  => now()->subDays(2)->format('Y-m-d'),
            'due_date'       => now()->addDays(5)->format('Y-m-d'),
            'return_date'    => null,
        ]);

        $response = $this->actingAs($this->clerk)
            ->postJson("/api/rentals/{$rental->rental_id}/cancel");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental cancelled successfully');

        // Rental status must be 'cancelled'
        $this->assertDatabaseHas('rentals', [
            'rental_id' => $rental->rental_id,
            'status_id' => $cancelledRntStatus->status_id,
        ]);

        // Reservation status must be 'cancelled'
        $this->assertDatabaseHas('reservations', [
            'reservation_id' => $reservation->reservation_id,
            'status_id'      => $cancelledResStatus->status_id,
        ]);

        // Item must be back to 'available'
        $this->assertDatabaseHas('inventories', [
            'item_id'   => $item->item_id,
            'status_id' => $availableStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_021 – checkOverdue() : Not overdue
    // =========================================================================

    /**
     * WBT_RNT_021: checkOverdue() - No status change when due_date is in the future.
     * Tested indirectly via batchCheckOverdue().
     */
    public function test_check_overdue_not_overdue(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $this->overdueRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/rentals/batch-check-overdue');

        $response->assertStatus(200)
                 ->assertJsonPath('overdue', 0);

        // Status must remain unchanged
        $this->assertDatabaseHas('rentals', [
            'rental_id' => $rental->rental_id,
            'status_id' => $activeStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_022 – checkOverdue() : Overdue status update
    // =========================================================================

    /**
     * WBT_RNT_022: checkOverdue() - Rental status updated to 'Overdue' when past due_date;
     * penalty invoice created via batchCheckOverdue().
     */
    public function test_check_overdue_status_update(): void
    {
        $activeStatus  = $this->activeRentalStatus();
        $overdueStatus = $this->overdueRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $this->paidPaymentStatus(); // Required by createOrUpdatePenaltyInvoice

        // Seed penalty rate setting
        RentalSetting::factory()->create([
            'setting_key'   => 'penalty_rate_per_day',
            'setting_value' => '50',
            'setting_type'  => 'float',
            'setting_group' => 'penalty',
        ]);
        RentalSetting::factory()->create([
            'setting_key'   => 'penalty_grace_period_hours',
            'setting_value' => '0',
            'setting_type'  => 'integer',
            'setting_group' => 'penalty',
        ]);
        RentalSetting::factory()->create([
            'setting_key'   => 'max_penalty_days',
            'setting_value' => '0',
            'setting_type'  => 'integer',
            'setting_group' => 'penalty',
        ]);

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($availableStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => now()->subDays(2)->format('Y-m-d'),
            'return_date' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/rentals/batch-check-overdue');

        $response->assertStatus(200)
                 ->assertJsonPath('overdue', 1);

        // Status must have changed to 'Overdue'
        $this->assertDatabaseHas('rentals', [
            'rental_id' => $rental->rental_id,
            'status_id' => $overdueStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_023 – checkOverdue() : Already returned
    // =========================================================================

    /**
     * WBT_RNT_023: checkOverdue() - Returns early without status change when
     * rental already has a return_date.
     */
    public function test_check_overdue_already_returned(): void
    {
        $returnedStatus  = $this->returnedRentalStatus();
        $this->overdueRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $returnedStatus, [
            'due_date'    => now()->subDays(3)->format('Y-m-d'),
            'return_date' => now()->subDay()->format('Y-m-d'), // Already returned
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/rentals/batch-check-overdue');

        $response->assertStatus(200)
                 // batchCheckOverdue only checks rentals where return_date IS NULL
                 ->assertJsonPath('overdue', 0);

        // Status must not have changed to Overdue
        $this->assertDatabaseHas('rentals', [
            'rental_id' => $rental->rental_id,
            'status_id' => $returnedStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_RNT_024 – calculatePenalty() : Within grace period
    // =========================================================================

    /**
     * WBT_RNT_024: calculatePenalty() - Returns 0 when returned within grace period.
     * Tested indirectly via show() endpoint which exposes calculated_penalty.
     */
    public function test_calculate_penalty_within_grace_period(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();

        // 24-hour grace period
        RentalSetting::factory()->create(['setting_key' => 'penalty_rate_per_day',       'setting_value' => '50',  'setting_type' => 'float',   'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'penalty_grace_period_hours', 'setting_value' => '24', 'setting_type' => 'integer', 'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'max_penalty_days',           'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($availableStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => now()->subHour()->format('Y-m-d'),     // Due 1 hour ago
            'return_date' => now()->format('Y-m-d'),                 // Returned now → within 24h grace
        ]);

        $response = $this->actingAs($this->clerk)
            ->getJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('calculated_penalty', 0);
    }

    // =========================================================================
    // WBT_RNT_025 – calculatePenalty() : Late return – 2 days
    // =========================================================================

    /**
     * WBT_RNT_025: calculatePenalty() - Returns ₱150 for 2.5 days late
     * (ceil(2.5) = 3 × ₱50).
     */
    public function test_calculate_penalty_late_return_two_days(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();

        RentalSetting::factory()->create(['setting_key' => 'penalty_rate_per_day',       'setting_value' => '50', 'setting_type' => 'float',   'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'penalty_grace_period_hours', 'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'max_penalty_days',           'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($availableStatus);

        $dueDate    = now()->subDays(3)->startOfDay();  // 3 days ago
        $returnDate = now()->subHours(12)->format('Y-m-d'); // returned 2.5 days late → ceil = 3 days

        $rental = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => $dueDate->format('Y-m-d'),
            'return_date' => $returnDate,
        ]);

        $response = $this->actingAs($this->clerk)
            ->getJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(200);
        $this->assertEquals(150.0, $response->json('calculated_penalty'));
    }

    // =========================================================================
    // WBT_RNT_026 – calculatePenalty() : Max penalty cap
    // =========================================================================

    /**
     * WBT_RNT_026: calculatePenalty() - Returns ₱250 when 10 days late but
     * max_penalty_days = 5 (capped at 5 × ₱50).
     */
    public function test_calculate_penalty_max_penalty_cap(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();

        RentalSetting::factory()->create(['setting_key' => 'penalty_rate_per_day',       'setting_value' => '50', 'setting_type' => 'float',   'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'penalty_grace_period_hours', 'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'max_penalty_days',           'setting_value' => '5',  'setting_type' => 'integer', 'setting_group' => 'penalty']);

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($availableStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => now()->subDays(10)->format('Y-m-d'),
            'return_date' => now()->format('Y-m-d'), // 10 days late but capped at 5
        ]);

        $response = $this->actingAs($this->clerk)
            ->getJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('calculated_penalty', 250);
    }

    // =========================================================================
    // WBT_RNT_027 – calculatePenalty() : Still active and overdue
    // =========================================================================

    /**
     * WBT_RNT_027: calculatePenalty() - Returns ₱150 for active rental 3 days overdue
     * (return_date = null, 3 days past due_date × ₱50).
     */
    public function test_calculate_penalty_still_active_and_overdue(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();

        RentalSetting::factory()->create(['setting_key' => 'penalty_rate_per_day',       'setting_value' => '50', 'setting_type' => 'float',   'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'penalty_grace_period_hours', 'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'max_penalty_days',           'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);

        $customer = $this->makeCustomer();
        $item     = $this->makeInventoryItem($availableStatus);
        $rental   = $this->makeActiveRental($customer, $item, $activeStatus, [
            'due_date'    => now()->subDays(3)->startOfDay()->format('Y-m-d'),
            'return_date' => null, // Still active
        ]);

        $response = $this->actingAs($this->clerk)
            ->getJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('is_overdue', true);

        $this->assertEquals(150.0, $response->json('calculated_penalty'));
    }

    // =========================================================================
    // WBT_RNT_028 – batchCheckOverdue() : Mixed active rentals
    // =========================================================================

    /**
     * WBT_RNT_028: batchCheckOverdue() - checked = 3; overdue = 2;
     * penalties_created_or_updated = 2; HTTP 200.
     */
    public function test_batch_check_overdue_mixed_active_rentals(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $this->overdueRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $this->paidPaymentStatus();

        RentalSetting::factory()->create(['setting_key' => 'penalty_rate_per_day',       'setting_value' => '50', 'setting_type' => 'float',   'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'penalty_grace_period_hours', 'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);
        RentalSetting::factory()->create(['setting_key' => 'max_penalty_days',           'setting_value' => '0',  'setting_type' => 'integer', 'setting_group' => 'penalty']);

        $customer = $this->makeCustomer();

        // 2 overdue rentals
        $item1 = $this->makeInventoryItem($availableStatus);
        $item2 = $this->makeInventoryItem($availableStatus);
        $this->makeActiveRental($customer, $item1, $activeStatus, ['due_date' => now()->subDays(2)->format('Y-m-d')]);
        $this->makeActiveRental($customer, $item2, $activeStatus, ['due_date' => now()->subDays(5)->format('Y-m-d')]);

        // 1 not yet due
        $item3 = $this->makeInventoryItem($availableStatus);
        $this->makeActiveRental($customer, $item3, $activeStatus, ['due_date' => now()->addDays(3)->format('Y-m-d')]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/rentals/batch-check-overdue');

        $response->assertStatus(200)
                 ->assertJsonPath('checked', 3)
                 ->assertJsonPath('overdue', 2)
                 ->assertJsonPath('penalties_created_or_updated', 2);
    }

    // =========================================================================
    // WBT_RNT_029 – destroy() : Has invoices
    // =========================================================================

    /**
     * WBT_RNT_029: destroy() - HTTP 422 when rental has associated invoices.
     */
    public function test_destroy_has_invoices(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $unpaidStatus    = $this->unpaidPaymentStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        Invoice::factory()->create([
            'rental_id'   => $rental->rental_id,
            'customer_id' => $customer->customer_id,
            'status_id'   => $unpaidStatus->status_id,
        ]);

        $response = $this->actingAs($this->clerk)
            ->deleteJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot delete rental. It has invoices associated with it.');

        // Rental must still exist
        $this->assertDatabaseHas('rentals', ['rental_id' => $rental->rental_id]);
    }

    // =========================================================================
    // WBT_RNT_030 – destroy() : Clean deletion
    // =========================================================================

    /**
     * WBT_RNT_030: destroy() - Rental deleted; HTTP 200.
     */
    public function test_destroy_clean_deletion(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $rental          = $this->makeActiveRental($customer, $item, $activeStatus);

        $response = $this->actingAs($this->clerk)
            ->deleteJson("/api/rentals/{$rental->rental_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Rental deleted successfully');

        $this->assertDatabaseMissing('rentals', ['rental_id' => $rental->rental_id]);
    }

    // =========================================================================
    // WBT_RNT_031 – index() : Overdue filter
    // =========================================================================

    /**
     * WBT_RNT_031: index() - rental_status=overdue returns only rentals where
     * return_date IS NULL AND due_date < now().
     */
    public function test_index_overdue_filter(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $overdueStatus   = $this->overdueRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();

        // Overdue rental
        $item1   = $this->makeInventoryItem($availableStatus);
        $rental1 = $this->makeActiveRental($customer, $item1, $overdueStatus, [
            'due_date'    => now()->subDays(2)->format('Y-m-d'),
            'return_date' => null,
        ]);

        // Active (not overdue) rental
        $item2   = $this->makeInventoryItem($availableStatus);
        $this->makeActiveRental($customer, $item2, $activeStatus, [
            'due_date'    => now()->addDays(3)->format('Y-m-d'),
            'return_date' => null,
        ]);

        $response = $this->actingAs($this->clerk)
            ->getJson('/api/rentals?rental_status=overdue');

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('rental_id');
        $this->assertContains($rental1->rental_id, $returnedIds->all());
    }

    // =========================================================================
    // WBT_RNT_032 – generateCSV() : Deposit report type
    // =========================================================================

    /**
     * WBT_RNT_032: generateCSV() - CSV includes 'Deposit Management Report' header,
     * summary rows, and deposit detail rows.
     */
    public function test_generate_csv_deposit_report_type(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $this->makeActiveRental($customer, $item, $activeStatus, ['deposit_amount' => 500]);

        $response = $this->actingAs($this->admin)
            ->get('/api/rentals/reports/csv?report_type=deposits');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Deposit Management Report', $content);
        $this->assertStringContainsString('Deposit Summary', $content);
        $this->assertStringContainsString('Detailed Deposit Information', $content);
    }

    // =========================================================================
    // WBT_RNT_033 – generateCSV() : Default rental summary
    // =========================================================================

    /**
     * WBT_RNT_033: generateCSV() - Default report_type produces 'Rental Report'
     * header and 'Rental Details' section.
     */
    public function test_generate_csv_default_rental_summary(): void
    {
        $activeStatus    = $this->activeRentalStatus();
        $availableStatus = $this->availableInventoryStatus();
        $customer        = $this->makeCustomer();
        $item            = $this->makeInventoryItem($availableStatus);
        $this->makeActiveRental($customer, $item, $activeStatus);

        $response = $this->actingAs($this->admin)
            ->get('/api/rentals/reports/csv');
        // No report_type param → defaults to 'rental_summary'

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Rental Report', $content);
        $this->assertStringContainsString('Rental Details', $content);
    }

    // =========================================================================
    // WBT_RNT_034 – getSettings() : Success
    // =========================================================================

    /**
     * WBT_RNT_034: getSettings() - HTTP 200; data includes 'settings' array
     * and 'grouped' object keyed by setting_group.
     */
    public function test_get_settings_success(): void
    {
        RentalSetting::factory()->create([
            'setting_key'   => 'penalty_rate_per_day',
            'setting_value' => '50',
            'setting_type'  => 'float',
            'setting_group' => 'penalty',
        ]);

        RentalSetting::factory()->create([
            'setting_key'   => 'default_rental_days',
            'setting_value' => '3',
            'setting_type'  => 'integer',
            'setting_group' => 'general',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/rentals/settings');

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => [
                         'settings',
                         'grouped',
                     ],
                 ]);

        // grouped must be keyed by setting_group
        $grouped = $response->json('data.grouped');
        $this->assertArrayHasKey('penalty', $grouped);
        $this->assertArrayHasKey('general', $grouped);
    }

    // =========================================================================
    // WBT_RNT_035 – updateSettings() : Valid penalty rate
    // =========================================================================

    /**
     * WBT_RNT_035: updateSettings() - penalty_rate_per_day updated; cache cleared;
     * HTTP 200 with success = true.
     */
    public function test_update_settings_valid_penalty_rate(): void
    {
        RentalSetting::factory()->create([
            'setting_key'   => 'penalty_rate_per_day',
            'setting_value' => '50',
            'setting_type'  => 'float',
            'setting_group' => 'penalty',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/rentals/settings', [
                'settings' => [
                    'penalty_rate_per_day' => 75,
                ],
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('message', 'Rental settings updated successfully');

        // The setting must have been updated in the DB
        $this->assertDatabaseHas('rental_settings', [
            'setting_key'   => 'penalty_rate_per_day',
            'setting_value' => '75',
        ]);
    }
}
