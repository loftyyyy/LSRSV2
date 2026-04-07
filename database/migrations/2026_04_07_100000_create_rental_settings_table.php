<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('setting_key')->unique();
            $table->text('setting_value');
            $table->string('setting_type')->default('string'); // string, integer, decimal, boolean, json
            $table->string('setting_group')->default('general'); // general, penalties, notifications, etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('rental_settings')->insert([
            [
                'setting_key' => 'penalty_rate_per_day',
                'setting_value' => '50.00',
                'setting_type' => 'decimal',
                'setting_group' => 'penalties',
                'description' => 'Penalty amount charged per day for overdue rentals',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'penalty_grace_period_hours',
                'setting_value' => '0',
                'setting_type' => 'integer',
                'setting_group' => 'penalties',
                'description' => 'Grace period in hours before penalty starts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'max_penalty_days',
                'setting_value' => '30',
                'setting_type' => 'integer',
                'setting_group' => 'penalties',
                'description' => 'Maximum number of days to charge penalty (0 = unlimited)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'notification_due_days_before',
                'setting_value' => '1',
                'setting_type' => 'integer',
                'setting_group' => 'notifications',
                'description' => 'Days before due date to send reminder notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'notification_overdue_enabled',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'setting_group' => 'notifications',
                'description' => 'Enable overdue notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'default_rental_days',
                'setting_value' => '3',
                'setting_type' => 'integer',
                'setting_group' => 'general',
                'description' => 'Default rental period in days',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'max_extension_count',
                'setting_value' => '3',
                'setting_type' => 'integer',
                'setting_group' => 'general',
                'description' => 'Maximum number of extensions allowed per rental',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_settings');
    }
};
