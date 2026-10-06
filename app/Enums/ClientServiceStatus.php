<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ClientServiceStatus: string
{
    use EnumHelpers;

    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Paused => 'Paused',
            self::Completed => 'Completed',
        };
    }
}
