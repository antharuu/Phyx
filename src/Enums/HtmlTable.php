<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum HtmlTable
{
    case SpecialChars;
    case Entities;

    public function native(): int
    {
        return match ($this) {
            self::SpecialChars => HTML_SPECIALCHARS,
            self::Entities => HTML_ENTITIES,
        };
    }
}
