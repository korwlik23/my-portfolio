<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasLocalizedContent, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'description_th',
        'description_en',
        'case_study_th',
        'case_study_en',
        'live_demo_url',
        'github_url',
        'image_path',
        'is_featured',
        'is_public',
        'display_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(ProjectFeature::class)->orderBy('display_order');
    }

    public function techStacks(): HasMany
    {
        return $this->hasMany(ProjectTechStack::class)->orderBy('name');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
