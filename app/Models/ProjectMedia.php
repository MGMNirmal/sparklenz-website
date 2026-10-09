<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['project_id', 'path', 'alt', 'caption', 'width', 'height', 'sort_order'])]
class ProjectMedia extends Model
{
    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
