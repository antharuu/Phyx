<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum Rounding
{
    case HalfUp;
    case HalfDown;
    case HalfEven;
    case HalfOdd;
    case TowardZero;
    case AwayFromZero;
}
