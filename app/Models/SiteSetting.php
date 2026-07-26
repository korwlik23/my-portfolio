<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function pairs(): array
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }

        return static::query()
            ->pluck('value', 'key')
            ->all();
    }

    public static function value(string $key, mixed $default = null): mixed
    {
        return static::pairs()[$key] ?? $default;
    }
}
