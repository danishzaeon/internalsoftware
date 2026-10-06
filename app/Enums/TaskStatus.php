<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum TaskStatus: string
{
    use EnumHelpers;

    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'To Do',
            self::InProgress => 'In Progress',
            self::Review => 'In Review',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isClosed(): bool
    {
        return $this === self::Completed || $this === self::Cancelled;
    }
}
