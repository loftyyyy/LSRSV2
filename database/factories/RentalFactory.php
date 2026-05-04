<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => \App\Models\Reservation::factory(),
            'item_id' => \App\Models\Inventory::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'released_by' => \App\Models\User::factory(),
            'released_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'original_due_date' => now()->addDays(7)->format('Y-m-d'),
            'status_id' => null,
        ];
    }
}
