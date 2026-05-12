<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum SortMode
{
    case Regular;
    case Numeric;
    case String;
    case Natural;
}
