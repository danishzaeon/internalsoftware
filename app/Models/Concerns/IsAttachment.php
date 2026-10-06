<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared behaviour for file attachment models (task_attachments, content_attachments).
 * Both tables store: file_name (original), file_path (random stored path), file_type, file_size.
 */
trait IsAttachment
{
    /** Never expose the storage path in JSON/array output. Files are served via an authorized controller. */
    public function initializeIsAttachment(): void
    {
        $this->makeHidden('file_path');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->file_type, 'image/');
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return ($i === 0 ? (string) $bytes : number_format($bytes, 1)).' '.$units[$i];
    }
}
