<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Translation\Translator as BaseTranslator;

class CustomTranslator extends BaseTranslator
{
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;
        $segments = explode('.', $key, 2);

        if (count($segments) === 2) {
            [$group, $item] = $segments;
            $overrides = Translation::getOverrides();

            if (isset($overrides[$locale][$group][$item])) {
                return $this->makeReplacements($overrides[$locale][$group][$item], $replace);
            }
        }

        return parent::get($key, $replace, $locale, $fallback);
    }
}
