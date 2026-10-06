<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ApprovalStatus: string
{
    use EnumHelpers;

    case Approved = 'approved';
    case Rejected = 'rejected';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Revision => 'Revision Requested',
        };
    }
}
