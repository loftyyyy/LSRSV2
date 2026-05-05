<?php

namespace Database\Factories;

use App\Models\RentalSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalSettingFactory extends Factory
{
    protected $model = RentalSetting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'setting_key' => $this->faker->unique()->word(),
            'setting_value' => $this->faker->word(),
            'setting_type' => 'string',
            'setting_group' => 'general',
            'description' => $this->faker->sentence(),
        ];
    }

    public function penaltyRate(): static
    {
        return $this->state([
            'setting_key' => 'penalty_rate_per_day',
            'setting_value' => '50.00',
            'setting_type' => 'decimal',
            'setting_group' => 'penalties',
            'description' => 'Daily penalty rate in PHP',
        ]);
    }

    public function gracePeriod(): static
    {
        return $this->state([
            'setting_key' => 'penalty_grace_period_hours',
            'setting_value' => '0',
            'setting_type' => 'integer',
            'setting_group' => 'penalties',
            'description' => 'Grace period in hours before penalty applies',
        ]);
    }

    public function maxPenaltyDays(): static
    {
        return $this->state([
            'setting_key' => 'max_penalty_days',
            'setting_value' => '30',
            'setting_type' => 'integer',
            'setting_group' => 'penalties',
            'description' => 'Maximum number of days penalty can be charged',
        ]);
    }
}
