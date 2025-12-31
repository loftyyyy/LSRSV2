<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventory_statuses')->insert([
            ['status_name' => 'available'],
            ['status_name' => 'rented'],
            ['status_name' => 'maintenance'],
            ['status_name' => 'retired'],
        ]);
    }
}
