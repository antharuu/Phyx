<?php

namespace Phyx\Str;

use Phyx\Enums\CaseSensitivity;

trait HandleSearch
{
    public static function contains(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
    ): bool
    {
        if ($search === '') {
            return true;
        }

        return match ($case) {
            CaseSensitivity::Sensitive => str_contains($value, $search),
            CaseSensitivity::Insensitive => mb_stripos($value, $search) !== false,
        };
    }
}