<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    use HasLocalizedContent;

    protected $fillable = [
        'key',
        'title_th',
        'title_en',
        'subtitle_th',
        'subtitle_en',
        'body_th',
        'body_en',
        'primary_cta_text_th',
        'primary_cta_text_en',
        'primary_cta_url',
        'secondary_cta_text_th',
        'secondary_cta_text_en',
        'secondary_cta_url',
        'settings',
        'image_path',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
}
