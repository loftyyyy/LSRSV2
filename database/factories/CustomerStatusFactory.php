<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerStatus>
 */
class CustomerStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status_name' => $this->faker->word(),
            'reason' => $this->faker->sentence(),
        ];
    }
}
