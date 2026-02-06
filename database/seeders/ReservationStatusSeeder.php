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
         $statuses = ['pending', 'confirmed', 'cancelled', 'expired'];

         foreach ($statuses as $status) {
             DB::table('reservation_statuses')->updateOrInsert(
                 ['status_name' => $status],
                 ['status_name' => $status]
             );
         }
     }
}
