<?php

namespace App\Enums\Concerns;

/**
 * Shared helpers for string-backed enums. Every enum using this trait must define label().
 */
trait EnumHelpers
{
    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string, string> value => human label */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
