<?php

namespace App\Models;

use App\Enums\ClientServiceStatus;
use App\Enums\ClientStatus;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'company_name', 'client_type', 'email', 'phone', 'whatsapp',
        'website', 'address', 'city', 'state', 'status', 'account_manager_id', 'notes',
    ];

    protected $attributes = [
        'client_type' => 'other',
        'status' => 'prospect',
    ];

    protected function casts(): array
    {
        return [
            'client_type' => ClientType::class,
            'status' => ClientStatus::class,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Active->value);
    }

    /** Used by the "project service must be active for this client" validation rule (Phase 4). */
    public function hasActiveService(int $serviceId): bool
    {
        return $this->clientServices()
            ->where('service_id', $serviceId)
            ->where('status', ClientServiceStatus::Active->value)
            ->exists();
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    /** Client-role logins that belong to this client. */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(ClientContact::class)->where('is_primary', true);
    }

    public function clientServices(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }

    public function activeClientServices(): HasMany
    {
        return $this->clientServices()->where('status', ClientServiceStatus::Active->value);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** Leads that were converted into this client. */
    public function convertedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'converted_client_id');
    }
}
