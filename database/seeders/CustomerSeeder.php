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
                'first_name' => 'Anne',
                'last_name' => 'Villanueva',
                'email' => 'anne.villanueva@example.com',
                'contact_number' => '09181234567',
                'address' => '118 Maharlika Ave, Pasig',
                'measurement' => ['bust' => 32, 'waist' => 25, 'hips' => 35, 'height_cm' => 158],
                'status_id' => $activeStatusId,
            ],
            [
                'first_name' => 'Carlo',
                'last_name' => 'Reyes',
                'email' => 'carlo.reyes@example.com',
                'contact_number' => '09201234567',
                'address' => '22 Molave St, Makati',
                'measurement' => ['chest' => 40, 'waist' => 33, 'inseam' => 31, 'height_cm' => 175],
                'status_id' => $activeStatusId,
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Torres',
                'email' => 'miguel.torres@example.com',
                'contact_number' => '09211234567',
                'address' => '81 Narra Rd, Taguig',
                'measurement' => ['chest' => 38, 'waist' => 32, 'inseam' => 30, 'height_cm' => 172],
                'status_id' => $activeStatusId,
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Garcia',
                'email' => 'daniel.garcia@example.com',
                'contact_number' => '09221234567',
                'address' => '63 Acacia Lane, Manila',
                'measurement' => ['chest' => 42, 'waist' => 35, 'inseam' => 32, 'height_cm' => 178],
                'status_id' => $activeStatusId,
            ],
            [
                'first_name' => 'Trisha',
                'last_name' => 'Lopez',
                'email' => 'trisha.lopez@example.com',
                'contact_number' => '09231234567',
                'address' => '15 Mabini Ave, Marikina',
                'measurement' => ['bust' => 35, 'waist' => 28, 'hips' => 37, 'height_cm' => 164],
                'status_id' => $activeStatusId,
            ],
            [
                'first_name' => 'Ella',
                'last_name' => 'Domingo',
                'email' => 'ella.domingo@example.com',
                'contact_number' => '09241234567',
                'address' => '90 Ortigas Ave, Pasig',
                'measurement' => ['bust' => 33, 'waist' => 26, 'hips' => 35, 'height_cm' => 160],
                'status_id' => $inactiveStatusId ?: $activeStatusId,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                array_merge($customer, ['updated_by' => $defaultUserId])
            );
        }
    }
}
