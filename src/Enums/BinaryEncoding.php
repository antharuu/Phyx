<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Binary-to-text encodings supported by bytes helpers.
 */
enum BinaryEncoding
{
    case Hex;
    case Base64;
    case Base64Url;
}
