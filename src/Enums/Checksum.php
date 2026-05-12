<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Lightweight non-cryptographic checksum algorithms for byte strings.
 */
enum Checksum
{
    case Crc32;
    case Adler32;
}
