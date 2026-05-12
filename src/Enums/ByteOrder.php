<?php

declare(strict_types=1);

namespace Phyx\Enums;

/**
 * Byte order variants for binary packing APIs.
 */
enum ByteOrder
{
    case BigEndian;
    case LittleEndian;
    case Machine;
}
