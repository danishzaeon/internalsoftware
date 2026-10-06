<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * One row per client decision. Append-only history: rows are never edited or deleted,
 * and each row records which content version the decision applies to.
 */
class ContentApproval extends Model
{
    /** content_id and reviewer_id are set explicitly by the RecordApproval action. */
    protected $fillable = ['content_version', 'status', 'comment', 'reviewed_at'];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'content_version' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Content approvals are append-only and cannot be edited.');
        });

        static::deleting(function () {
            throw new LogicException('Content approvals are append-only and cannot be deleted.');
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class, 'content_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
