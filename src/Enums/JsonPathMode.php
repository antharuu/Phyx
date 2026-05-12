<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum JsonPathMode
{
    case Null;
    case Throw;
    case Default;
}
