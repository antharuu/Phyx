<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum MissingValue
{
    case Null;
    case Throw;
    case Default;
}
