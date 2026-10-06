<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot model for project_members. The table has its own auto-increment id
 * and a UNIQUE(project_id, user_id) constraint that prevents duplicates.
 */
class ProjectMember extends Pivot
{
    protected $table = 'project_members';

    public $incrementing = true;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
