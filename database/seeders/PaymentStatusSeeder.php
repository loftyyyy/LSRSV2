<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_statuses')->insert([
            ['status_name' => 'pending'],
            ['status_name' => 'paid'],
            ['status_name' => 'refunded'],
            ['status_name' => 'cancelled'],
            ['status_name' => 'voided'],
            ['status_name' => 'failed'],

        ]);
    }
}
