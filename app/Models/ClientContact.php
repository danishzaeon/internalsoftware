<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientContact extends Model
{
    use SoftDeletes;

    /** client_id is set through the relationship ($client->contacts()->create()). */
    protected $fillable = ['name', 'designation', 'email', 'phone', 'whatsapp', 'is_primary'];

    protected $attributes = ['is_primary' => false];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
