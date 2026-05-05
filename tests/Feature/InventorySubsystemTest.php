<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Models\InventoryStatus;
use App\Models\InventoryVariant;
use App\Models\Rental;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventorySubsystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    // ---------------------------------------------------------------------------
    // Shared helper: create all four standard statuses in one call.
    // Individual tests only need to call the ones relevant to their branch.
    // ---------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user  = User::factory()->create(['is_admin' => false]);
    }

    private function createAvailableStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'available']);
    }

    private function createMaintenanceStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'maintenance']);
    }

    private function createRetiredStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'retired']);
    }

    private function createRentedStatus(): InventoryStatus
    {
        return InventoryStatus::factory()->create(['status_name' => 'rented']);
    }

    /**
     * Minimal valid payload for store() – tests override individual fields as needed.
     */
    private function baseStorePayload(array $overrides = []): array
    {
        return array_merge([
            'item_type'    => 'gown',
            'name'         => 'Test Gown',
            'size'         => 'M',
            'color'        => 'White',
            'design'       => 'Floral',
            'rental_price' => 500,
            'deposit_amount' => 200,
        ], $overrides);
    }

    // =========================================================================
    // WBT_INV_001 – store() : SKU conflict when quantity > 1
    // =========================================================================

    /**
     * WBT_INV_001: store() - SKU must be empty for bulk creation.
     */
    public function test_store_sku_conflict_quantity_greater_than_one(): void
    {
        $this->createAvailableStatus();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload([
                'quantity' => 3,
                'sku'      => 'TEST-001',
            ]));

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'SKU must be empty when creating multiple items so unique SKUs can be auto-generated.');
    }

    // =========================================================================
    // WBT_INV_002 – store() : Default 'available' status when status_id omitted
    // =========================================================================

    /**
     * WBT_INV_002: store() - Item created with available status when status_id omitted.
     */
    public function test_store_default_available_status(): void
    {
        $availableStatus = $this->createAvailableStatus();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload());

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status_id', $availableStatus->status_id);

        $this->assertDatabaseHas('inventories', [
            'name'      => 'Test Gown',
            'status_id' => $availableStatus->status_id,
        ]);
    }

    // =========================================================================
    // WBT_INV_003 – store() : Bulk creation (quantity = 3)
    // =========================================================================

    /**
     * WBT_INV_003: store() - 3 items created, message and created_count reflect quantity.
     */
    public function test_store_bulk_creation_quantity_three(): void
    {
        $this->createAvailableStatus();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload(['quantity' => 3]));

        $response->assertStatus(201)
                 ->assertJsonPath('message', '3 inventory items created successfully')
                 ->assertJsonPath('created_count', 3);

        $this->assertDatabaseCount('inventories', 3);
    }

    // =========================================================================
    // WBT_INV_004 – store() : Single image upload – first image auto-set as primary
    // =========================================================================

    /**
     * WBT_INV_004: store() - First uploaded image automatically becomes primary.
     */
    public function test_store_image_upload_primary_auto_set(): void
    {
        Storage::fake('public');
        $this->createAvailableStatus();

        $file = UploadedFile::fake()->image('front.jpg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', array_merge(
                $this->baseStorePayload(['quantity' => 1]),
                [
                    'images' => [
                        ['file' => $file, 'view_type' => 'front'],
                    ],
                ]
            ));

        $response->assertStatus(201);

        // The single uploaded image must be flagged as primary
        $this->assertDatabaseHas('inventory_images', ['is_primary' => true]);
    }

    // =========================================================================
    // WBT_INV_005 – store() : Two images – second explicitly marked as primary
    // =========================================================================

    /**
     * WBT_INV_005: store() - Explicitly marking second image as primary overrides the first.
     */
    public function test_store_image_upload_override_primary(): void
    {
        Storage::fake('public');
        $this->createAvailableStatus();

        $file1 = UploadedFile::fake()->image('front.jpg');
        $file2 = UploadedFile::fake()->image('back.jpg');

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', array_merge(
                $this->baseStorePayload(['quantity' => 1]),
                [
                    'images' => [
                        ['file' => $file1, 'view_type' => 'front', 'is_primary' => 0],
                        ['file' => $file2, 'view_type' => 'back',  'is_primary' => 1],
                    ],
                ]
            ));

        $response->assertStatus(201);

        $images = InventoryImage::all();
        $this->assertCount(2, $images);

        // Exactly one image must be primary
        $this->assertCount(1, $images->where('is_primary', true));

        // The primary image must be the second one (back view)
        $this->assertEquals('back', $images->firstWhere('is_primary', true)->view_type);

        // The first image must NOT be primary
        $this->assertFalse((bool) $images->firstWhere('view_type', 'front')->is_primary);
    }

    // =========================================================================
    // WBT_INV_006 – destroy() : Has active rentals
    // =========================================================================

    /**
     * WBT_INV_006: destroy() - Cannot delete item with an active rental (return_date IS NULL).
     */
    public function test_destroy_has_active_rentals(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        Rental::factory()->create([
            'item_id'     => $inventory->item_id,
            'return_date' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/inventories/{$inventory->item_id}");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot delete inventory item with active rentals or reservations');
    }

    // =========================================================================
    // WBT_INV_007 – destroy() : Has active reservations
    // =========================================================================

    /**
     * WBT_INV_007: destroy() - Cannot delete item linked to a non-cancelled reservation.
     */
    public function test_destroy_has_active_reservations(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $reservationStatus = ReservationStatus::factory()->create(['status_name' => 'pending']);
        $reservation = Reservation::factory()->create(['status_id' => $reservationStatus->status_id]);

        ReservationItem::factory()->create([
            'item_id'        => $inventory->item_id,
            'reservation_id' => $reservation->reservation_id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/inventories/{$inventory->item_id}");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot delete inventory item with active rentals or reservations');
    }

    // =========================================================================
    // WBT_INV_008 – destroy() : Clean deletion with images
    // =========================================================================

    /**
     * WBT_INV_008: destroy() - Images deleted from storage; item deleted; variant counters refreshed.
     */
    public function test_destroy_clean_deletion(): void
    {
        Storage::fake('public');

        $availableStatus = $this->createAvailableStatus();
        $variant = InventoryVariant::factory()->create();
        $inventory = Inventory::factory()->create([
            'status_id'  => $availableStatus->status_id,
            'variant_id' => $variant->variant_id,
        ]);

        // Create a fake image file and record
        $fakePath = "inventory/{$inventory->item_id}/test.jpg";
        Storage::disk('public')->put($fakePath, 'fake-image-content');

        InventoryImage::factory()->create([
            'item_id'    => $inventory->item_id,
            'image_path' => $fakePath,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/inventories/{$inventory->item_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Inventory item and all associated images deleted successfully');

        // Image file must be gone from storage
        Storage::disk('public')->assertMissing($fakePath);

        // Image record must be gone
        $this->assertDatabaseMissing('inventory_images', ['item_id' => $inventory->item_id]);

        // Inventory record must be gone
        $this->assertDatabaseMissing('inventories', ['item_id' => $inventory->item_id]);

        // Variant counters must reflect 0 units remaining
        $this->assertDatabaseHas('inventory_variants', [
            'variant_id'  => $variant->variant_id,
            'total_units' => 0,
        ]);
    }

    // =========================================================================
    // WBT_INV_009 – updateStatus() : Note required for 'maintenance'
    // =========================================================================

    /**
     * WBT_INV_009: updateStatus() - 422 if no note provided for maintenance status.
     */
    public function test_update_status_note_required_for_maintenance(): void
    {
        $availableStatus    = $this->createAvailableStatus();
        $maintenanceStatus  = $this->createMaintenanceStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/inventories/{$inventory->item_id}/status", [
                'status_id' => $maintenanceStatus->status_id,
                // status_note intentionally omitted
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'A note is required when setting status to Maintenance.');
    }

    // =========================================================================
    // WBT_INV_010 – updateStatus() : Note required for 'retired'
    // =========================================================================

    /**
     * WBT_INV_010: updateStatus() - 422 if no note provided for retired status.
     */
    public function test_update_status_note_required_for_retired(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $retiredStatus   = $this->createRetiredStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/inventories/{$inventory->item_id}/status", [
                'status_id' => $retiredStatus->status_id,
                // status_note intentionally omitted
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'A note is required when setting status to Retired.');
    }

    // =========================================================================
    // WBT_INV_011 – updateStatus() : Setting 'available' clears the note
    // =========================================================================

    /**
     * WBT_INV_011: updateStatus() - status_note set to null when transitioning to available.
     */
    public function test_update_status_available_clears_note(): void
    {
        $availableStatus   = $this->createAvailableStatus();
        $maintenanceStatus = $this->createMaintenanceStatus();

        $inventory = Inventory::factory()->create([
            'status_id'   => $maintenanceStatus->status_id,
            'status_note' => 'Zip damaged',
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/inventories/{$inventory->item_id}/status", [
                'status_id' => $availableStatus->status_id,
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Inventory status updated successfully');

        $this->assertDatabaseHas('inventories', [
            'item_id'     => $inventory->item_id,
            'status_id'   => $availableStatus->status_id,
            'status_note' => null,
        ]);
    }

    // =========================================================================
    // WBT_INV_012 – updateStatus() : Successful maintenance with note
    // =========================================================================

    /**
     * WBT_INV_012: updateStatus() - Status + note saved; variant counters refreshed.
     */
    public function test_update_status_successful_maintenance_with_note(): void
    {
        $availableStatus   = $this->createAvailableStatus();
        $maintenanceStatus = $this->createMaintenanceStatus();

        $variant   = InventoryVariant::factory()->create();
        $inventory = Inventory::factory()->create([
            'status_id'  => $availableStatus->status_id,
            'variant_id' => $variant->variant_id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/inventories/{$inventory->item_id}/status", [
                'status_id'   => $maintenanceStatus->status_id,
                'status_note' => 'Zip damaged',
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Inventory status updated successfully');

        $this->assertDatabaseHas('inventories', [
            'item_id'     => $inventory->item_id,
            'status_id'   => $maintenanceStatus->status_id,
            'status_note' => 'Zip damaged',
        ]);

        // Variant counters must have been recalculated (0 available units after moving to maintenance)
        $this->assertDatabaseHas('inventory_variants', [
            'variant_id'      => $variant->variant_id,
            'available_units' => 0,
        ]);
    }

    // =========================================================================
    // WBT_INV_013 – update() : Variant re-resolved when attribute changes
    // =========================================================================

    /**
     * WBT_INV_013: update() - Changing size triggers resolveVariantId; old/new variant counters refreshed.
     */
    public function test_update_variant_re_resolution_on_attribute_change(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $oldVariant = InventoryVariant::factory()->create([
            'item_type' => 'gown', 'name' => 'Test Gown',
            'size' => 'M', 'color' => 'White', 'design' => 'Floral',
            'rental_price' => 500, 'deposit_amount' => 200,
        ]);
        $inventory = Inventory::factory()->create([
            'status_id'  => $availableStatus->status_id,
            'variant_id' => $oldVariant->variant_id,
            'item_type'  => 'gown',
            'name'       => 'Test Gown',
            'size'       => 'M',
            'color'      => 'White',
            'design'     => 'Floral',
            'rental_price'   => 500,
            'deposit_amount' => 200,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/inventories/{$inventory->item_id}", [
                'item_type'    => 'gown',
                'name'         => 'Test Gown',
                'size'         => 'XL', // Changed
                'color'        => 'White',
                'design'       => 'Floral',
                'rental_price' => 500,
                'deposit_amount' => 200,
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Inventory item updated successfully');

        $inventory->refresh();
        // variant_id must have changed since size changed
        $this->assertNotEquals($oldVariant->variant_id, $inventory->variant_id);

        // Old variant counters must have been refreshed
        $this->assertDatabaseHas('inventory_variants', [
            'variant_id'  => $oldVariant->variant_id,
            'total_units' => 0,
        ]);
    }

    // =========================================================================
    // WBT_INV_014 – update() : Explicit variant_id = null triggers resolution
    // =========================================================================

    /**
     * WBT_INV_014: update() - Passing variant_id = null causes resolveVariantId to be called.
     */
    public function test_update_explicit_variant_id_null(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $existingVariant = InventoryVariant::factory()->create([
            'item_type' => 'gown', 'name' => 'Test Gown',
            'size' => 'M', 'color' => 'White', 'design' => 'Floral',
            'rental_price' => 500, 'deposit_amount' => 200,
        ]);
        $inventory = Inventory::factory()->create([
            'status_id'  => $availableStatus->status_id,
            'variant_id' => $existingVariant->variant_id,
            'item_type'  => 'gown',
            'name'       => 'Test Gown',
            'size'       => 'M',
            'color'      => 'White',
            'design'     => 'Floral',
            'rental_price'   => 500,
            'deposit_amount' => 200,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/inventories/{$inventory->item_id}", [
                'variant_id'   => null,
                'item_type'    => 'gown',
                'name'         => 'Test Gown',
                'size'         => 'M',
                'color'        => 'White',
                'design'       => 'Floral',
                'rental_price' => 500,
                'deposit_amount' => 200,
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Inventory item updated successfully');

        // variant_id must have been resolved (not left null)
        $inventory->refresh();
        $this->assertNotNull($inventory->variant_id);
    }

    // =========================================================================
    // WBT_INV_015 – checkAvailability() : Item is available
    // =========================================================================

    /**
     * WBT_INV_015: checkAvailability() - available = true when no conflicts exist.
     */
    public function test_check_availability_available(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $startDate = now()->addDays(10)->format('Y-m-d');
        $endDate   = now()->addDays(15)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->getJson("/api/inventories/{$inventory->item_id}/availability?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
                 ->assertJsonPath('available', true)
                 ->assertJsonPath('conflicts.has_rentals', false)
                 ->assertJsonPath('conflicts.has_reservations', false);
    }

    // =========================================================================
    // WBT_INV_016 – checkAvailability() : Conflicting rental
    // =========================================================================

    /**
     * WBT_INV_016: checkAvailability() - available = false when active rental overlaps.
     */
    public function test_check_availability_conflicting_rental(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        // Rental with no return_date overlapping the queried range
        Rental::factory()->create([
            'item_id'       => $inventory->item_id,
            'return_date'   => null,
            'released_date' => now()->addDays(8)->format('Y-m-d'),
            'due_date'      => now()->addDays(20)->format('Y-m-d'),
        ]);

        $startDate = now()->addDays(10)->format('Y-m-d');
        $endDate   = now()->addDays(15)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->getJson("/api/inventories/{$inventory->item_id}/availability?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
                 ->assertJsonPath('available', false)
                 ->assertJsonPath('conflicts.has_rentals', true);
    }

    // =========================================================================
    // WBT_INV_017 – checkAvailability() : Conflicting reservation
    // =========================================================================

    /**
     * WBT_INV_017: checkAvailability() - available = false when non-cancelled reservation overlaps.
     */
    public function test_check_availability_conflicting_reservation(): void
    {
        $availableStatus     = $this->createAvailableStatus();
        $reservationStatus   = ReservationStatus::factory()->create(['status_name' => 'pending']);
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $reservation = Reservation::factory()->create([
            'status_id'  => $reservationStatus->status_id,
            'start_date' => now()->addDays(9)->format('Y-m-d'),
            'end_date'   => now()->addDays(16)->format('Y-m-d'),
        ]);

        ReservationItem::factory()->create([
            'item_id'        => $inventory->item_id,
            'reservation_id' => $reservation->reservation_id,
        ]);

        $startDate = now()->addDays(10)->format('Y-m-d');
        $endDate   = now()->addDays(15)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->getJson("/api/inventories/{$inventory->item_id}/availability?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
                 ->assertJsonPath('available', false)
                 ->assertJsonPath('conflicts.has_reservations', true);
    }

    // =========================================================================
    // WBT_INV_018 – getAvailableItems() : Date range excludes conflicting rental
    // =========================================================================

    /**
     * WBT_INV_018: getAvailableItems() - Item with conflicting rental excluded; free item returned.
     */
    public function test_get_available_items_date_range_exclusion(): void
    {
        $availableStatus = $this->createAvailableStatus();

        $itemA = Inventory::factory()->create(['status_id' => $availableStatus->status_id, 'name' => 'Item A']);
        $itemB = Inventory::factory()->create(['status_id' => $availableStatus->status_id, 'name' => 'Item B']);

        // Item A has an active rental overlapping the queried dates
        Rental::factory()->create([
            'item_id'       => $itemA->item_id,
            'return_date'   => null,
            'released_date' => now()->addDays(8)->format('Y-m-d'),
            'due_date'      => now()->addDays(20)->format('Y-m-d'),
        ]);

        $startDate = now()->addDays(10)->format('Y-m-d');
        $endDate   = now()->addDays(15)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->getJson("/api/inventories/available?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('item_id');
        $this->assertContains($itemB->item_id, $returnedIds->all());
        $this->assertNotContains($itemA->item_id, $returnedIds->all());
    }

    // =========================================================================
    // WBT_INV_019 – bulkUpdateStatus() : Multiple items, per-variant counter refresh
    // =========================================================================

    /**
     * WBT_INV_019: bulkUpdateStatus() - All items updated; variant counters refreshed per unique variant.
     */
    public function test_bulk_update_status_multiple_items(): void
    {
        $availableStatus   = $this->createAvailableStatus();
        $maintenanceStatus = $this->createMaintenanceStatus();

        $variantA = InventoryVariant::factory()->create();
        $variantB = InventoryVariant::factory()->create();

        $item1 = Inventory::factory()->create(['status_id' => $availableStatus->status_id, 'variant_id' => $variantA->variant_id]);
        $item2 = Inventory::factory()->create(['status_id' => $availableStatus->status_id, 'variant_id' => $variantA->variant_id]);
        $item3 = Inventory::factory()->create(['status_id' => $availableStatus->status_id, 'variant_id' => $variantB->variant_id]);

        $response = $this->actingAs($this->user)
            ->putJson('/api/inventories/update/bulk', [
                'item_ids'  => [$item1->item_id, $item2->item_id, $item3->item_id],
                'status_id' => $maintenanceStatus->status_id,
            ]);

        $response->assertStatus(200)
                 ->assertJsonPath('updated_count', 3);

        // All 3 items must now have maintenance status
        foreach ([$item1, $item2, $item3] as $item) {
            $this->assertDatabaseHas('inventories', [
                'item_id'   => $item->item_id,
                'status_id' => $maintenanceStatus->status_id,
            ]);
        }

        // Variant A: 2 units total, 0 available (both now in maintenance)
        $this->assertDatabaseHas('inventory_variants', [
            'variant_id'      => $variantA->variant_id,
            'available_units' => 0,
        ]);

        // Variant B: 1 unit total, 0 available
        $this->assertDatabaseHas('inventory_variants', [
            'variant_id'      => $variantB->variant_id,
            'available_units' => 0,
        ]);
    }

    // =========================================================================
    // WBT_INV_020 – report() : inventory_summary type
    // =========================================================================

    /**
     * WBT_INV_020: report() - inventory_summary returns required keys.
     */
    public function test_report_inventory_summary_type(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->count(2)->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=inventory_summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'title',
                     'items',
                     'total_count',
                     'total_value',
                     'by_status',
                     'items_with_complete_images',
                 ]);
    }

    // =========================================================================
    // WBT_INV_021 – report() : availability_report type
    // =========================================================================

    /**
     * WBT_INV_021: report() - availability_report returns required keys.
     */
    public function test_report_availability_report_type(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->count(2)->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=availability_report');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_available',
                     'by_type',
                     'by_size',
                     'available_items',
                 ]);
    }

    // =========================================================================
    // WBT_INV_022 – report() : rental_history type
    // =========================================================================

    /**
     * WBT_INV_022: report() - rental_history returns total_rentals and nested rentals.
     */
    public function test_report_rental_history_type(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);
        Rental::factory()->create(['item_id' => $inventory->item_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=rental_history');

        $response->assertStatus(200)
                 ->assertJsonStructure(['total_rentals', 'items']);

        $this->assertGreaterThanOrEqual(1, $response->json('total_rentals'));
    }

    // =========================================================================
    // WBT_INV_023 – report() : condition_report type
    // =========================================================================

    /**
     * WBT_INV_023: report() - condition_report returns by_condition bucketed into excellent/good/fair/poor.
     */
    public function test_report_condition_report_type(): void
    {
        $availableStatus   = $this->createAvailableStatus();
        $maintenanceStatus = $this->createMaintenanceStatus();
        $retiredStatus     = $this->createRetiredStatus();
        $this->createRentedStatus();

        Inventory::factory()->create(['status_id' => $availableStatus->status_id]);
        Inventory::factory()->create(['status_id' => $maintenanceStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=condition_report');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'by_condition' => ['excellent', 'good', 'fair', 'poor'],
                 ]);
    }

    // =========================================================================
    // WBT_INV_024 – report() : revenue_by_item type
    // =========================================================================

    /**
     * WBT_INV_024: report() - revenue_by_item returns total_revenue, total_rentals, and items.
     */
    public function test_report_revenue_by_item_type(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=revenue_by_item');

        $response->assertStatus(200)
                 ->assertJsonStructure(['total_revenue', 'total_rentals', 'items']);
    }

    // =========================================================================
    // WBT_INV_025 – report() : Unknown type falls through to inventory_summary
    // =========================================================================

    /**
     * WBT_INV_025: report() - Unrecognised report_type defaults to inventory_summary.
     */
    public function test_report_unknown_type_defaults_to_inventory_summary(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->count(2)->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/inventories/reports/generate?report_type=unknown_type');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'title',
                     'items',
                     'total_count',
                     'total_value',
                     'by_status',
                 ]);
    }

    // =========================================================================
    // WBT_INV_026 – generateCSV() : inventory_summary CSV
    // =========================================================================

    /**
     * WBT_INV_026: generateCSV() - CSV includes BOM, statistics section, and item detail rows.
     */
    public function test_generate_csv_inventory_summary(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->count(2)->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->get('/api/inventories/reports/csv?report_type=inventory_summary');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();

        // UTF-8 BOM
        $this->assertStringContainsString("\xEF\xBB\xBF", $content);

        // Statistics section
        $this->assertStringContainsString('Total Items', $content);
        $this->assertStringContainsString('Total Value', $content);

        // Item detail rows header
        $this->assertStringContainsString('Item Details', $content);
    }

    // =========================================================================
    // WBT_INV_027 – generateCSV() : revenue_by_item CSV
    // =========================================================================

    /**
     * WBT_INV_027: generateCSV() - Revenue CSV includes total revenue, total rentals, and per-item rows.
     */
    public function test_generate_csv_revenue_by_item(): void
    {
        $availableStatus = $this->createAvailableStatus();
        Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->admin)
            ->get('/api/inventories/reports/csv?report_type=revenue_by_item');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Total Revenue', $content);
        $this->assertStringContainsString('Total Rentals', $content);
        $this->assertStringContainsString('Item Revenue Details', $content);
    }

    // =========================================================================
    // WBT_INV_028 – updateCondition() : Deprecated endpoint
    // =========================================================================

    /**
     * WBT_INV_028: updateCondition() - Always returns 422 with deprecation message.
     */
    public function test_update_condition_deprecated_endpoint(): void
    {
        $availableStatus = $this->createAvailableStatus();
        $inventory = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/inventories/{$inventory->item_id}/condition", [
                'condition' => 'good',
            ]);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Condition updates are no longer supported. Use inventory status updates (available, rented, maintenance, retired).');
    }

    // =========================================================================
    // WBT_INV_029 – index() : Filter has_images = true
    // =========================================================================

    /**
     * WBT_INV_029: index() - has_images=true returns only items with at least one image.
     */
    public function test_index_filter_has_images_true(): void
    {
        $availableStatus = $this->createAvailableStatus();

        $itemWithImage    = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);
        $itemWithoutImage = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        InventoryImage::factory()->create(['item_id' => $itemWithImage->item_id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/inventories?has_images=true');

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('item_id');
        $this->assertContains($itemWithImage->item_id, $returnedIds->all());
        $this->assertNotContains($itemWithoutImage->item_id, $returnedIds->all());
    }

    // =========================================================================
    // WBT_INV_030 – index() : Filter has_images = false
    // =========================================================================

    /**
     * WBT_INV_030: index() - has_images=false returns only items without images.
     */
    public function test_index_filter_has_images_false(): void
    {
        $availableStatus = $this->createAvailableStatus();

        $itemWithImage    = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);
        $itemWithoutImage = Inventory::factory()->create(['status_id' => $availableStatus->status_id]);

        InventoryImage::factory()->create(['item_id' => $itemWithImage->item_id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/inventories?has_images=false');

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('item_id');
        $this->assertContains($itemWithoutImage->item_id, $returnedIds->all());
        $this->assertNotContains($itemWithImage->item_id, $returnedIds->all());
    }

    // =========================================================================
    // WBT_INV_031 – resolveVariantId() : Existing variant matched by SKU
    // =========================================================================

    /**
     * WBT_INV_031: resolveVariantId() - Existing variant reused when variant_sku matches.
     */
    public function test_resolve_variant_id_existing_variant_sku_match(): void
    {
        $this->createAvailableStatus();

        $existingVariant = InventoryVariant::factory()->create([
            'variant_sku' => 'GWN-0001',
            'item_type'   => 'gown',
        ]);

        $variantCountBefore = InventoryVariant::count();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', array_merge(
                $this->baseStorePayload(),
                ['variant_sku' => 'GWN-0001']
            ));

        $response->assertStatus(201);

        // No new variant must have been created
        $this->assertEquals($variantCountBefore, InventoryVariant::count());

        // The created item must be linked to the existing variant
        $this->assertDatabaseHas('inventories', [
            'variant_id' => $existingVariant->variant_id,
        ]);
    }

    // =========================================================================
    // WBT_INV_032 – resolveVariantId() : Attribute match (no SKU provided)
    // =========================================================================

    /**
     * WBT_INV_032: resolveVariantId() - Existing variant reused when attributes match.
     */
    public function test_resolve_variant_id_attribute_match_no_sku(): void
    {
        $this->createAvailableStatus();

        // Pre-create a variant matching the payload's attributes exactly
        $existingVariant = InventoryVariant::factory()->create([
            'item_type'      => 'gown',
            'name'           => 'Test Gown',
            'size'           => 'M',
            'color'          => 'White',
            'design'         => 'Floral',
            'rental_price'   => 500,
            'deposit_amount' => 200,
            'is_sellable'    => false,
            'selling_price'  => null,
        ]);

        $variantCountBefore = InventoryVariant::count();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload());

        $response->assertStatus(201);

        // No new variant must have been created
        $this->assertEquals($variantCountBefore, InventoryVariant::count());

        $this->assertDatabaseHas('inventories', [
            'variant_id' => $existingVariant->variant_id,
        ]);
    }

    // =========================================================================
    // WBT_INV_033 – resolveVariantId() : New variant created for unique attributes
    // =========================================================================

    /**
     * WBT_INV_033: resolveVariantId() - New InventoryVariant created with auto-generated SKU.
     */
    public function test_resolve_variant_id_new_variant_created(): void
    {
        $this->createAvailableStatus();
        // No pre-existing variants
        $this->assertEquals(0, InventoryVariant::count());

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload([
                'name'   => 'Totally Unique Gown',
                'color'  => 'Magenta',
                'design' => 'StarBurst',
            ]));

        $response->assertStatus(201);

        // Exactly one variant must now exist with an auto-generated SKU
        $this->assertEquals(1, InventoryVariant::count());

        $variant = InventoryVariant::first();
        $this->assertMatchesRegularExpression('/^GWN-\d{4}$/', $variant->variant_sku);
    }

    // =========================================================================
    // WBT_INV_034 – generateVariantSku() : Gown prefix
    // =========================================================================

    /**
     * WBT_INV_034: generateVariantSku() - Gown item_type produces 'GWN-0001' SKU.
     */
    public function test_generate_variant_sku_gown_prefix(): void
    {
        $this->createAvailableStatus();
        // No existing GWN variants
        $this->assertEquals(0, InventoryVariant::where('variant_sku', 'like', 'GWN-%')->count());

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload(['item_type' => 'gown']));

        $response->assertStatus(201);

        $this->assertDatabaseHas('inventory_variants', ['variant_sku' => 'GWN-0001']);
    }

    // =========================================================================
    // WBT_INV_035 – generateVariantSku() : Default prefix for unknown item_type
    // =========================================================================

    /**
     * WBT_INV_035: generateVariantSku() - item_type other than gown/suit produces 'VAR-0001' SKU.
     */
    public function test_generate_variant_sku_default_prefix_for_unknown_type(): void
    {
        $this->createAvailableStatus();

        $response = $this->actingAs($this->user)
            ->postJson('/api/inventories', $this->baseStorePayload(['item_type' => 'accessory']));

        $response->assertStatus(201);

        $this->assertDatabaseHas('inventory_variants', ['variant_sku' => 'VAR-0001']);
    }
}
