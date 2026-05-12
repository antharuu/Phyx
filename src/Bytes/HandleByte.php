<?php

declare(strict_types=1);

namespace Phyx\Bytes;

use ValueError;

/**
 * Single-byte integer conversion helpers for {@see \Phyx\Bytes}.
 */
trait HandleByte
{
    /**
     * Convert an integer byte value to a single-byte string.
     *
     * Maps an integer in the range 0-255 to its corresponding ASCII/binary
     * character. Values outside this range will trigger a ValueError.
     *
     * @param  int  $byte  The integer value (0-255) to convert.
     * @return string  A single-byte string.
     *
     * @throws ValueError When the integer is not within the 0-255 range.
     *
     * @example Bytes::fromInt(65) // => 'A'
     * @example Bytes::fromInt(0) // => "\x00"
     *
     * @see chr
     */
    public static function fromInt(int $byte): string
    {
        if ($byte < 0 || $byte > 255) {
            throw new ValueError('Byte value must be between 0 and 255.');
        }

        return chr($byte);
    }

    /**
     * Convert a single-byte string to its integer value.
     *
     * Returns the numeric byte value (0-255) of the provided byte. The input
     * must be exactly one byte long.
     *
     * @param  string  $byte  A single-byte string.
     * @return int  The integer value (0-255).
     *
     * @throws ValueError When the string length is not exactly 1.
     *
     * @example Bytes::toInt('A') // => 65
     * @example Bytes::toInt("\x00") // => 0
     *
     * @see ord
     */
    public static function toInt(string $byte): int
    {
        if (strlen($byte) !== 1) {
            throw new ValueError('Expected exactly one byte.');
        }

        return ord($byte);
    }

    /**
     * Convert a byte string into an array of integer byte values.
     *
     * Iterates through each byte of the string and collects their numeric
     * representations (0-255). This is a binary-safe operation.
     *
     * @param  string  $bytes  The raw byte string to process.
     * @return list<int>  A list of byte values.
     *
     * @example Bytes::ints('ABC') // => [65, 66, 67]
     * @example Bytes::ints("\x00\xFF") // => [0, 255]
     *
     * @see ord
     */
    public static function ints(string $bytes): array
    {
        $values = [];
        $length = strlen($bytes);

        for ($offset = 0; $offset < $length; $offset++) {
            $values[] = ord($bytes[$offset]);
        }

        return $values;
    }

    /**
     * Create a byte string from a sequence of integer byte values.
     *
     * Concatenates bytes generated from the provided integers. Each integer
     * must be in the range 0-255.
     *
     * @param  iterable<int>  $bytes  The sequence of byte values (0-255).
     * @return string  The resulting raw byte string.
     *
     * @throws ValueError When any integer is not within the 0-255 range.
     *
     * @example Bytes::fromInts([65, 66, 67]) // => 'ABC'
     * @example Bytes::fromInts([0, 255]) // => "\x00\xFF"
     *
     * @see chr
     */
    public static function fromInts(iterable $bytes): string
    {
        $result = '';

        foreach ($bytes as $byte) {
            $result .= self::fromInt($byte);
        }

        return $result;
    }
}
