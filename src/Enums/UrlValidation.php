<?php

declare(strict_types=1);

namespace Phyx\Enums;

enum UrlValidation
{
    case Absolute;
    case Relative;
    case Http;
}
