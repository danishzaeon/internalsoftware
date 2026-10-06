<?php

namespace App\Models;

use App\Models\Concerns\IsAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentAttachment extends Model
{
    use IsAttachment, SoftDeletes;

    protected $fillable = ['file_name', 'file_path', 'file_type', 'file_size'];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class, 'content_id');
    }
}
