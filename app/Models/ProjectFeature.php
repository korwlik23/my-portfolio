<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFeature extends Model
{
    use HasLocalizedContent;

    protected $fillable = ['project_id', 'description_th', 'description_en', 'display_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
