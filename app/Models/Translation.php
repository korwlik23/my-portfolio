<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Translation extends Model
{
    public const CACHE_KEY = 'translation_overrides';

    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    public static function getOverrides(): array
    {
        try {
            if (!Schema::hasTable('translations')) {
                return [];
            }

            return Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()
                    ->get(['locale', 'group', 'key', 'value'])
                    ->groupBy('locale')
                    ->map(fn ($localeRows) => $localeRows
                        ->groupBy('group')
                        ->map(fn ($groupRows) => $groupRows->pluck('value', 'key')->all())
                        ->all())
                    ->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
