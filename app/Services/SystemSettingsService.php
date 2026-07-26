<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemSettingsService
{
    private const FILE_PATH = 'system_settings.json';
    private static ?array $settingsCache = null;

    public function all(): array
    {
        if (self::$settingsCache !== null) {
            return self::$settingsCache;
        }

        return self::$settingsCache = (function () {
            if ($this->hasSystemSettingsTable()) {
                $settings = SystemSetting::query()
                    ->get()
                    ->mapWithKeys(fn (SystemSetting $setting) => [
                        $setting->key => is_array($setting->value) && array_key_exists('value', $setting->value)
                            ? $setting->value['value']
                            : $setting->value,
                    ])
                    ->all();

                return array_merge($this->defaults(), $settings);
            }

            $settings = $this->fileSettings();

            return array_merge($this->defaults(), $settings);
        })();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function update(array $settings): array
    {
        $settings = array_merge($this->all(), $settings);

        if (!$this->hasSystemSettingsTable()) {
            Storage::disk('local')->put(self::FILE_PATH, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            self::$settingsCache = null;

            return $settings;
        }

        foreach ($settings as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => ['value' => $value]]
            );
        }

        self::$settingsCache = null;

        return $settings;
    }

    public function defaults(): array
    {
        return [
            'require_email_verification' => true,
            'bypass_password_validation' => false,
            'brand_name' => config('app.name'),
            'logo_path' => null,
        ];
    }

    private function fileSettings(): array
    {
        if (!Storage::disk('local')->exists(self::FILE_PATH)) {
            return [];
        }

        $settings = json_decode(Storage::disk('local')->get(self::FILE_PATH), true);

        return is_array($settings) ? $settings : [];
    }

    private function hasSystemSettingsTable(): bool
    {
        try {
            return Schema::hasTable('system_settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
