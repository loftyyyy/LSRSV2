<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReservationItemAllocation>
 */
class ReservationItemAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'allocation_status' => 'allocated',
            'allocated_at' => now(),
            'released_at' => null,
            'returned_at' => null,
        ];
    }
}
