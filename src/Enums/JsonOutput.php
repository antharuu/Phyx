<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum JsonOutput: int
{
    case Compact = 0;
    case Pretty = JSON_PRETTY_PRINT;
    case UnescapedUnicode = JSON_UNESCAPED_UNICODE;
    case UnescapedSlashes = JSON_UNESCAPED_SLASHES;
}
