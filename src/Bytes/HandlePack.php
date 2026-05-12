<?php

declare(strict_types=1);

namespace Phyx\Bytes;

use ValueError;

/**
 * Binary pack and unpack helpers for {@see \Phyx\Bytes}.
 */
trait HandlePack
{
    /**
     * Pack data into a binary string according to a format.
     *
     * Wraps PHP's native pack function to create binary structures. The format
     * string defines the types and order of the packed values.
     *
     * @param  string  $format  The pack format string (e.g., 'n', 'V', 'C*').
     * @param  mixed   ...$values  The values to pack.
     * @return string  The resulting packed binary string.
     *
     * @example Bytes::pack('n', 1024) // => "\x04\x00"
     * @example Bytes::pack('C3', 65, 66, 67) // => 'ABC'
     *
     * @see pack
     */
    public static function pack(string $format, mixed ...$values): string
    {
        return pack($format, ...$values);
    }

    /**
     * Unpack data from a binary string according to a format.
     *
     * Reconstructs values from a binary string based on the provided format.
     * Returns an associative array of the unpacked data.
     *
     * @param  string  $format  The unpack format string.
     * @param  string  $bytes   The binary string to unpack.
     * @return array<int|string, mixed>  An array of unpacked values.
     *
     * @throws ValueError When the data cannot be unpacked with the given format.
     *
     * @example Bytes::unpack('n', "\x04\x00") // => [1 => 1024]
     * @example Bytes::unpack('C3', 'ABC') // => [1 => 65, 2 => 66, 3 => 67]
     *
     * @see unpack
     */
    public static function unpack(string $format, string $bytes): array
    {
        $unpacked = unpack($format, $bytes);

        if ($unpacked === false) {
            throw new ValueError('Unable to unpack bytes with the given format.');
        }

        return $unpacked;
    }
}
