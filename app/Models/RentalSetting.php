<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RentalSetting extends Model
{
    protected $table = 'rental_settings';

    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'setting_group',
        'description',
    ];

    /**
     * Get a setting value by key with automatic type casting
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $cacheKey = "rental_setting_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('setting_key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return static::castValue($setting->setting_value, $setting->setting_type);
        });
    }

    /**
     * Set a setting value by key
     */
    public static function setValue(string $key, mixed $value): bool
    {
        $setting = static::where('setting_key', $key)->first();

        if (! $setting) {
            return false;
        }

        $setting->setting_value = is_array($value) ? json_encode($value) : (string) $value;
        $setting->save();

        // Clear the cache for this setting
        Cache::forget("rental_setting_{$key}");
        Cache::forget('rental_settings_all');

        return true;
    }

    /**
     * Get all settings grouped by setting_group
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('rental_settings_all', 3600, function () {
            $settings = static::all();
            $grouped = [];

            foreach ($settings as $setting) {
                $grouped[$setting->setting_group][$setting->setting_key] = [
                    'value' => static::castValue($setting->setting_value, $setting->setting_type),
                    'type' => $setting->setting_type,
                    'description' => $setting->description,
                ];
            }

            return $grouped;
        });
    }

    /**
     * Get all settings as a flat key-value array
     */
    public static function getAllFlat(): array
    {
        $settings = static::all();
        $flat = [];

        foreach ($settings as $setting) {
            $flat[$setting->setting_key] = static::castValue($setting->setting_value, $setting->setting_type);
        }

        return $flat;
    }

    /**
     * Clear all rental settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::all();

        foreach ($settings as $setting) {
            Cache::forget("rental_setting_{$setting->setting_key}");
        }

        Cache::forget('rental_settings_all');
    }

    /**
     * Cast the value to the appropriate type
     */
    protected static function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => (bool) (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get the penalty rate per day
     */
    public static function getPenaltyRate(): float
    {
        return (float) static::getValue('penalty_rate_per_day', 50.00);
    }

    /**
     * Get the grace period in hours
     */
    public static function getGracePeriodHours(): int
    {
        return (int) static::getValue('penalty_grace_period_hours', 0);
    }

    /**
     * Get the maximum penalty days
     */
    public static function getMaxPenaltyDays(): int
    {
        return (int) static::getValue('max_penalty_days', 30);
    }
}
