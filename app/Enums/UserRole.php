<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum UserRole: string
{
    use EnumHelpers;

    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Staff => 'Staff',
            self::Client => 'Client',
        };
    }

    public function isInternal(): bool
    {
        return $this !== self::Client;
    }

    public function isAdminLevel(): bool
    {
        return $this === self::SuperAdmin || $this === self::Admin;
    }
}
