<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Rental;
use App\Models\RentalStatus;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\User;
use Database\Factories\RentalStatusFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerSubsystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create(['is_admin' => false]);
    }

    /**
     * WBT_CUS_001: Customer – store() - Active Status Not Found
     */
    public function test_store_active_status_not_found(): void
    {
        // Precondition: No 'active' CustomerStatus record in DB.
        DB::table('customer_statuses')->delete();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_number' => '1234567890',
            'email' => 'john.doe@gmail.com',
            'address' => '123 Main St'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/customers', $data);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Active customer status not found.');
    }

    /**
     * WBT_CUS_002: Customer – store() - Successful Creation
     */
    public function test_store_successful_creation(): void
    {
        // Precondition: 'active' CustomerStatus exists.
        $status = CustomerStatus::factory()->create(['status_name' => 'active']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_number' => '1234567890',
            'email' => 'john.doe@gmail.com',
            'address' => '123 Main St'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/customers', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.status_id', $status->status_id)
                 ->assertJsonStructure(['data' => ['status']]);

        $this->assertDatabaseHas('customers', ['email' => 'john.doe@gmail.com']);
    }

    /**
     * WBT_CUS_003: Customer – store() - DB Exception Branch
     */
    public function test_store_db_exception_branch(): void
    {
        // Precondition: 'active' CustomerStatus exists, DB write fails (e.g., unique constraint).
        CustomerStatus::factory()->create(['status_name' => 'active']);

        $existingCustomer = Customer::factory()->create();


        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'contact_number' => '0987654321',
            'email' => $existingCustomer->email, // Duplicate email
            'address' => '456 Side St'
        ];

        $response = $this->actingAs($this->user)->postJson('/api/customers', $data);

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'The email has already been taken.');
    }

    /**
     * WBT_CUS_004: Customer – update() - Update Contact Info
     */
    public function test_update_contact_info(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        $customer = Customer::factory()->create();

        $data = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'contact_number' => '5551234567',
            'address' => '999 New Address St'
        ];

        $response = $this->actingAs($this->user)->putJson("/api/customers/{$customer->customer_id}", $data);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Customer updated successfully');

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'contact_number' => '5551234567',
            'address' => '999 New Address St'
        ]);
    }

    /**
     * WBT_CUS_005: Customer – destroy() - Has Active Rentals
     */
    public function test_destroy_has_active_rentals(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        $customer = Customer::factory()->create();

        // Active rental (return_date IS NULL)
        Rental::factory()->create([
            'customer_id' => $customer->customer_id,
            'return_date' => null,
            'status_id' => RentalStatus::factory()->create(['status_name' => 'active'])->status_id
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/customers/{$customer->customer_id}");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot delete customer with active rentals or reservations');
    }

    /**
     * WBT_CUS_006: Customer – destroy() - Has Active Reservations
     */
    public function test_destroy_has_active_reservations(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        $customer = Customer::factory()->create();

        $status = ReservationStatus::factory()->create(['status_name' => 'pending']);

        // Active reservation (status != cancelled)
        Reservation::factory()->create([
            'customer_id' => $customer->customer_id,
            'status_id' => $status->status_id
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/customers/{$customer->customer_id}");

        $response->assertStatus(422)
                 ->assertJsonPath('message', 'Cannot delete customer with active rentals or reservations');
    }

    /**
     * WBT_CUS_007: Customer – destroy() - Clean Deletion
     */
    public function test_destroy_clean_deletion(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/customers/{$customer->customer_id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Customer deleted successfully');

        // Verify clean deletion
        $this->assertDatabaseMissing('customers', ['customer_id' => $customer->customer_id]);
    }

    /**
     * WBT_CUS_008: Customer – deactivate() - Inactive Status Not Found
     */
    public function test_deactivate_inactive_status_not_found(): void
    {
        $status = CustomerStatus::factory()->create(['status_name' => 'active']);
        $customer = Customer::factory()->create(['status_id' => $status->status_id]);

        // Ensure no inactive status exists
        CustomerStatus::where('status_name', 'inactive')->delete();

        $response = $this->actingAs($this->user)->postJson("/api/customers/{$customer->customer_id}/deactivate");

        $response->assertStatus(404)
                 ->assertJsonPath('message', 'Inactive status not found in system');
    }

    /**
     * WBT_CUS_009: Customer – deactivate() - Successful Deactivation
     */
    public function test_deactivate_successful_deactivation(): void
    {
        $activeStatus = CustomerStatus::factory()->create(['status_name' => 'active']);
        $inactiveStatus = CustomerStatus::factory()->create(['status_name' => 'inactive']);

        $customer = Customer::factory()->create(['status_id' => $activeStatus->status_id]);

        $response = $this->actingAs($this->user)->postJson("/api/customers/{$customer->customer_id}/deactivate");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Customer deactivated successfully');

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'status_id' => $inactiveStatus->status_id
        ]);
    }

    /**
     * WBT_CUS_010: Customer – reactivate() - Active Status Not Found
     */
    public function test_reactivate_active_status_not_found(): void
    {
        $inactiveStatus = CustomerStatus::factory()->create(['status_name' => 'inactive']);
        $customer = Customer::factory()->create(['status_id' => $inactiveStatus->status_id]);

        // Ensure no active status exists
        CustomerStatus::where('status_name', 'active')->delete();

        $response = $this->actingAs($this->user)->postJson("/api/customers/{$customer->customer_id}/reactivate");

        $response->assertStatus(404)
                 ->assertJsonPath('message', 'Active status not found in system');
    }

    /**
     * WBT_CUS_011: Customer – reactivate() - Successful Reactivation
     */
    public function test_reactivate_successful_reactivation(): void
    {
        $activeStatus = CustomerStatus::factory()->create(['status_name' => 'active']);
        $inactiveStatus = CustomerStatus::factory()->create(['status_name' => 'inactive']);

        $customer = Customer::factory()->create(['status_id' => $inactiveStatus->status_id]);

        $response = $this->actingAs($this->user)->postJson("/api/customers/{$customer->customer_id}/reactivate");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Customer reactivated successfully');

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'status_id' => $activeStatus->status_id
        ]);
    }

    /**
     * WBT_CUS_012: Customer – index() - Search by Numeric ID
     */
    public function test_index_search_by_numeric_id(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);

        // Create some customers, ensuring they do not accidentally match the search ID
        $customer = Customer::factory()->create(['customer_id' => 9999]);
        for ($i = 1; $i <= 3; $i++) {
            Customer::factory()->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => "johndoe{$i}@gmail.com",
                'contact_number' => '09000000000'
            ]);
        }

        $response = $this->actingAs($this->user)->getJson("/api/customers?search={$customer->customer_id}");

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment([
            'customer_id' => 9999,
        ]);
    }

    /**
     * WBT_CUS_013: Customer – index() - Invalid Sort Column Fallback
     */
    public function test_index_invalid_sort_column_fallback(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        Customer::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->getJson('/api/customers?sort_by=malicious_column');

        $response->assertStatus(200);
        // Should not throw SQL error and default to created_at descending
    }

    /**
     * WBT_CUS_014: Customer – report() - Date Range + Status Filter
     */
    public function test_report_date_range_and_status_filter(): void
    {
        $activeStatus = CustomerStatus::factory()->create(['status_name' => 'active']);
        $inactiveStatus = CustomerStatus::factory()->create(['status_name' => 'inactive']);

        // Customer 1: Created yesterday, active
        Customer::factory()->create([
            'status_id' => $activeStatus->status_id,
            'created_at' => now()->subDay()
        ]);

        // Customer 2: Created yesterday, inactive (Filtered out by status)
        Customer::factory()->create([
            'status_id' => $inactiveStatus->status_id,
            'created_at' => now()->subDay()
        ]);

        // Customer 3: Created 10 days ago, active (Filtered out by date)
        Customer::factory()->create([
            'status_id' => $activeStatus->status_id,
            'created_at' => now()->subDays(10)
        ]);

        $startDate = now()->subDays(2)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->admin)->getJson("/api/customers/reports/generate?start_date={$startDate}&end_date={$endDate}&status_id={$activeStatus->status_id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('customers'));
    }

    /**
     * WBT_CUS_015: Customer – report() - No Filters
     */
    public function test_report_no_filters(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/customers/reports/generate');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('customers'));
        $this->assertEquals(3, $response->json('statistics.total_customers'));
    }

    /**
     * WBT_CUS_016: Customer – generateCSV() - Full Export
     */
    public function test_generate_csv_full_export(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/api/customers/reports/csv');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Customer Details', $content);
        $this->assertStringContainsString('Report Statistics', $content);
        $this->assertStringContainsString("\xEF\xBB\xBF", $content);
    }

    /**
     * WBT_CUS_017: Customer – generatePDF() - Full Export
     */
    public function test_generate_pdf_full_export(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);
        Customer::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get('/api/customers/reports/pdf');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * WBT_CUS_018: Customer – getRegistrationTrend() - Data Coverage
     */
    public function test_get_registration_trend(): void
    {
        CustomerStatus::factory()->create(['status_name' => 'active']);

        // 1 user this month, 2 users last month
        Customer::factory()->create(['created_at' => now()]);
        Customer::factory()->count(2)->create(['created_at' => now()->subMonth()]);

        $response = $this->actingAs($this->admin)->getJson('/api/customers/reports/registration-trend');

        $response->assertStatus(200)
                 ->assertJsonStructure(['months', 'data', 'total_registered']);

        $this->assertEquals(3, $response->json('total_registered'));
        $this->assertGreaterThanOrEqual(1, count($response->json('months')));
    }
}
