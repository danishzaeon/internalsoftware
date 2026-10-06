<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    /** lead_id and user_id are set explicitly by LogLeadActivity (never from request input). */
    protected $fillable = ['type', 'description', 'followup_at'];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'followup_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
