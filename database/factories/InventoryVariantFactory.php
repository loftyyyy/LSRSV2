<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryVariant>
 */
class InventoryVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'variant_sku' => $this->faker->unique()->bothify('VAR-####'),
            'item_type' => $this->faker->randomElement(['gown', 'suit', 'accessory']),
            'name' => $this->faker->word(),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'color' => $this->faker->colorName(),
            'design' => $this->faker->word(),
            'rental_price' => $this->faker->randomFloat(2, 50, 500),
            'deposit_amount' => $this->faker->randomFloat(2, 50, 500),
            'is_sellable' => false,
            'selling_price' => null,
            'total_units' => $this->faker->numberBetween(1, 10),
            'available_units' => $this->faker->numberBetween(0, 10),
        ];
    }
}
