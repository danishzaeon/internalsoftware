<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ClientStatus: string
{
    use EnumHelpers;

    case Active = 'active';
    case Inactive = 'inactive';
    case Prospect = 'prospect';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Prospect => 'Prospect',
        };
    }
}
