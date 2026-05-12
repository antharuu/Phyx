<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Controls how base64 padding is validated while decoding.
 */
enum PaddingMode
{
    case Strict;
    case Lenient;
}
