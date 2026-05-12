<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum HtmlDoctype
{
    case Html5;
    case Xhtml;

    public function isXhtml(): bool
    {
        return $this === self::Xhtml;
    }
}
