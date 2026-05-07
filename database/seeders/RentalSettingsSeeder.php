<?php

namespace Database\Seeders;

use App\Models\RentalSetting;
use Illuminate\Support\Facades\Log;

class RentalSettingsSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'penalty_rate_per_day',
                'setting_value' => '50.00',
                'setting_type' => 'decimal',
                'setting_group' => 'penalty',
                'description' => 'Penalty rate charged per day for overdue rentals',
            ],
            [
                'setting_key' => 'penalty_grace_period_hours',
                'setting_value' => '0',
                'setting_type' => 'integer',
                'setting_group' => 'penalty',
                'description' => 'Grace period in hours after due date before penalty starts',
            ],
            [
                'setting_key' => 'max_penalty_days',
                'setting_value' => '30',
                'setting_type' => 'integer',
                'setting_group' => 'penalty',
                'description' => 'Maximum number of days to charge penalty (0 = unlimited)',
            ],
            [
                'setting_key' => 'notification_due_days_before',
                'setting_value' => '2',
                'setting_type' => 'integer',
                'setting_group' => 'notification',
                'description' => 'Days before due date to send reminder notifications',
            ],
            [
                'setting_key' => 'notification_overdue_enabled',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'setting_group' => 'notification',
                'description' => 'Enable overdue notifications',
            ],
            [
                'setting_key' => 'default_rental_days',
                'setting_value' => '7',
                'setting_type' => 'integer',
                'setting_group' => 'general',
                'description' => 'Default number of days for new rentals',
            ],
            [
                'setting_key' => 'max_extension_count',
                'setting_value' => '3',
                'setting_type' => 'integer',
                'setting_group' => 'general',
                'description' => 'Maximum number of extensions allowed per rental',
            ],
        ];

        foreach ($settings as $setting) {
            RentalSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }

        Log::info('Rental settings seeded successfully.');
    }
}
