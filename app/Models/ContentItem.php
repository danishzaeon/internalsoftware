<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * status and version are NOT mass assignable: they only change through the content workflow Actions
     * (SubmitForClientReview, RecordApproval, ReopenContent) so the approval flow cannot be bypassed.
     * project_id and created_by are set explicitly at creation.
     */
    protected $fillable = ['title', 'content_type', 'content', 'scheduled_for', 'assigned_to'];

    protected $attributes = [
        'status' => 'draft',
        'version' => 1,
    ];

    protected function casts(): array
    {
        return [
            'content_type' => ContentType::class,
            'status' => ContentStatus::class,
            'scheduled_for' => 'datetime',
            'version' => 'integer',
        ];
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** The staff member responsible for this item (works like tasks.assigned_to). */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ContentAttachment::class, 'content_id');
    }

    /** Full, append-only approval history across all versions. */
    public function approvals(): HasMany
    {
        return $this->hasMany(ContentApproval::class, 'content_id')->orderBy('id');
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(ContentApproval::class, 'content_id')->latestOfMany();
    }
}
