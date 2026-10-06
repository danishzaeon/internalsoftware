<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    /** converted_client_id is NOT mass assignable: only ConvertLeadToClient sets it. */
    protected $fillable = [
        'name', 'company_name', 'email', 'phone', 'source', 'service_id',
        'requirement', 'status', 'assigned_to', 'expected_value',
        'next_followup_at', 'notes',
    ];

    protected $attributes = ['status' => 'new'];

    protected function casts(): array
    {
        return [
            'source' => LeadSource::class,
            'status' => LeadStatus::class,
            'expected_value' => 'decimal:2',
            'next_followup_at' => 'datetime',
        ];
    }

    public function isConverted(): bool
    {
        return $this->converted_client_id !== null;
    }

    /** Not won and not lost. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [LeadStatus::Won->value, LeadStatus::Lost->value]);
    }

    /** Open leads whose follow-up date has arrived. */
    public function scopeNeedsFollowUp(Builder $query): Builder
    {
        return $query->open()
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now());
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }
}
