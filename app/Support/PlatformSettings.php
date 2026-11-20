<?php

namespace App\Support;

use App\Models\PlatformSetting;

class PlatformSettings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return PlatformSetting::find($key)?->value ?? $default;
    }

    public static function set(string $key, mixed $value, ?int $updatedBy): void
    {
        PlatformSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'updated_by' => $updatedBy]
        );
    }
}
