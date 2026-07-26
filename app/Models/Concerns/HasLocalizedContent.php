<?php

namespace App\Models\Concerns;

trait HasLocalizedContent
{
    public function localized(string $field, ?string $locale = null, ?string $fallbackLocale = 'en'): ?string
    {
        $locale = in_array($locale ?: app()->getLocale(), ['th', 'en'], true) ? ($locale ?: app()->getLocale()) : 'en';
        $localizedField = "{$field}_{$locale}";
        $fallbackField = "{$field}_{$fallbackLocale}";

        return $this->{$localizedField} ?: ($this->{$fallbackField} ?? null);
    }
}
