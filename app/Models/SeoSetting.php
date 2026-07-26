<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasLocalizedContent;

    protected $fillable = [
        'page_key',
        'meta_title_th',
        'meta_title_en',
        'meta_description_th',
        'meta_description_en',
        'og_image_path',
        'keywords',
        'canonical_url',
    ];

    public function metaTitle(?string $locale = null): ?string
    {
        return $this->localized('meta_title', $locale);
    }

    public function metaDescription(?string $locale = null): ?string
    {
        return $this->localized('meta_description', $locale);
    }
}
