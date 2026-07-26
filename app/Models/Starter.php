<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Starter extends Model
{
    use HasLocalizedContent, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description_th',
        'description_en',
        'stack',
        'demo_url',
        'github_url',
        'status',
        'setup_notes_th',
        'setup_notes_en',
        'deploy_notes_th',
        'deploy_notes_en',
        'is_public',
        'display_order',
    ];

    protected $casts = [
        'stack' => 'array',
        'is_public' => 'boolean',
    ];

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
