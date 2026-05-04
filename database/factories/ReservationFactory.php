<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reserved_by' => \App\Models\User::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'status_id' => \App\Models\ReservationStatus::factory(),
            'reservation_date' => now()->format('Y-m-d'),
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
        ];
    }
}
