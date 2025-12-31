<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rental_statuses')->insert([
            ['status_name' => 'rented'],
            ['status_name' => 'returned'],
            ['status_name' => 'overdue'],
        ]);
    }
}
