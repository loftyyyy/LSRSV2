<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $this->call([
            UserSeeder::class,
            CustomerStatusSeeder::class,
            PaymentStatusSeeder::class,
            RentalStatusSeeder::class,
            ReservationStatusSeeder::class,
            InventoryStatusSeeder::class,
            CustomerSeeder::class,
            InventorySeeder::class,
            ReservationSeeder::class,
            ReservationItemSeeder::class,
            RentalSeeder::class,
            InvoiceSeeder::class,
            InvoiceItemSeeder::class,
            PaymentSeeder::class,
            InventoryImageSeeder::class,
        ]);
    }
}
