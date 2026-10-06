<?php

namespace App\Models;

use App\Enums\ClientServiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientService extends Model
{
    use SoftDeletes;

    /** client_id is set through the relationship ($client->clientServices()->create()). */
    protected $fillable = ['service_id', 'start_date', 'end_date', 'status', 'monthly_value', 'notes'];

    protected $attributes = ['status' => 'active'];

    protected function casts(): array
    {
        return [
            'status' => ClientServiceStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_value' => 'decimal:2',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ClientServiceStatus::Active->value);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
