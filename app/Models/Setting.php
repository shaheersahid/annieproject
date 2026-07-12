<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * Get a setting value by key with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("setting.{$key}", 3600, fn () => static::where('key', $key)->first());

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    /**
     * Set / upsert a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $stored = is_array($value) ? json_encode($value) : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Forget a cached setting.
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("setting.{$key}");
    }
}
