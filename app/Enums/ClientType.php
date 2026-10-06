<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ClientType: string
{
    use EnumHelpers;

    case Doctor = 'doctor';
    case Clinic = 'clinic';
    case Hospital = 'hospital';
    case Company = 'company';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Doctor => 'Doctor',
            self::Clinic => 'Clinic',
            self::Hospital => 'Hospital',
            self::Company => 'Company',
            self::Other => 'Other',
        };
    }
}
