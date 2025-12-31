<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reservation_status')->insert([
            ['status_name' => 'confirmed'],
            ['status_name' => 'cancelled'],
            ['status_name' => 'expired'],

        ]);
    }
}
