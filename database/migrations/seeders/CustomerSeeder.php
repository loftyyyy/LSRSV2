<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = CustomerStatus::whereRaw('LOWER(status_name) = ?', ['active'])->value('status_id');
        $inactiveStatusId = CustomerStatus::whereRaw('LOWER(status_name) = ?', ['inactive'])->value('status_id');
        $defaultUserId = DB::table('users')->value('user_id');

        $customers = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@example.com',
                'contact_number' => '09171234567',
                'address' => '34 Sampaguita St, Quezon City',
                'measurement' => ['bust' => 34, 'waist' => 27, 'hips' => 36, 'height_cm' => 162],
                'status_id' => $activeStatusId,
            ],

            [
                'first_name' => 'Rho',
                'last_name' => 'Alphonce',
                'email' => 'r.alphonce@example.com',
                'contact_number' => '09171234567',
                'address' => '34 Sampaguita St, Quezon City',
                'measurement' => ['bust' => 34, 'waist' => 27, 'hips' => 36, 'height_cm' => 162],
                'status_id' => $activeStatusId,
            ]
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                array_merge($customer, ['updated_by' => $defaultUserId])
            );
        }
    }
}
