<?php

namespace App\Models;

use App\Models\Concerns\IsAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use IsAttachment, SoftDeletes;

    /** Only the StoreUploadedFile action fills these, from server-side data (never from request input). */
    protected $fillable = ['file_name', 'file_path', 'file_type', 'file_size'];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
