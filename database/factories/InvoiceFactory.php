<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . $this->faker->randomNumber(8),
            'invoice_type' => 'reservation',
            'invoice_date' => now(),
            'total_amount' => $this->faker->randomFloat(2, 0, 1000),
            'amount_paid' => 0,
            'balance_due' => $this->faker->randomFloat(2, 0, 1000),
            'status_id' => 1, // Default to first payment status
            'created_by' => 1, // Default to first user
        ];
    }
}
