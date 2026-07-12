<?php

namespace App\Traits;

trait CastsSettingValue
{
    /**
     * Cast a raw DB string to its proper PHP type based on the stored type column.
     */
    public static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'json'    => json_decode($value, true) ?? [],
            default   => (string) $value, // 'string'
        };
    }
}
