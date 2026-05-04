<?php

namespace Database\Factories;

use App\Models\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'item_type' => $this->faker->randomElement(['gown', 'suit']),
            'sku' => $this->faker->unique()->lexify('???-###'),
            'size' => 'M',
            'color' => 'Red',
            'design' => $this->faker->randomElement(['Modern', 'Classic', 'Embellished']),
            'rental_price' => 500,
            'status_id' => InventoryStatus::factory()->state(['status_name'=>'active']),
        ];
    }
}
