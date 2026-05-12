<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum MatchMode
{
    case Glob;
    case Regex;
    case Literal;
}
