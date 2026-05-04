<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->userName() . '@gmail.com',
            'contact_number' => $this->faker->numerify('09#########'),
            'address' => $this->faker->address(),
            'status_id' => \App\Models\CustomerStatus::factory(), 
        ];
    }
}
