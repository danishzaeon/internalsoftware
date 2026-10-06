<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ActivityType: string
{
    use EnumHelpers;

    case Call = 'call';
    case Email = 'email';
    case Whatsapp = 'whatsapp';
    case Meeting = 'meeting';
    case Note = 'note';

    public function label(): string
    {
        return match ($this) {
            self::Call => 'Call',
            self::Email => 'Email',
            self::Whatsapp => 'WhatsApp',
            self::Meeting => 'Meeting',
            self::Note => 'Note',
        };
    }
}
