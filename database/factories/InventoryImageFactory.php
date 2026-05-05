<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryImage>
 */
class InventoryImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => \App\Models\Inventory::factory(),
            'image_path' => 'inventory/' . $this->faker->uuid() . '.jpg',
            'view_type' => $this->faker->randomElement(['front', 'back', 'side', 'detail']),
            'is_primary' => false,
        ];
    }
}
