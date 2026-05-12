<?php

declare(strict_types=1);

namespace Phyx\Bytes;

/**
 * Byte length and emptiness helpers for {@see \Phyx\Bytes}.
 */
trait HandleLength
{
    /**
     * Count the number of bytes in a string.
     *
     * Returns the absolute byte count without considering character encoding
     * or multibyte sequences.
     *
     * @param  string  $bytes  The byte string to measure.
     * @return int  The number of bytes.
     *
     * @example Bytes::length('Phyx') // => 4
     * @example Bytes::length("\x00\xFF") // => 2
     *
     * @see strlen
     */
    public static function length(string $bytes): int
    {
        return strlen($bytes);
    }

    /**
     * Check if the byte string is empty.
     *
     * Returns true if the string contains zero bytes.
     *
     * @param  string  $bytes  The byte string to check.
     * @return bool  True if empty, false otherwise.
     *
     * @example Bytes::isEmpty('') // => true
     * @example Bytes::isEmpty("\x00") // => false
     */
    public static function isEmpty(string $bytes): bool
    {
        return $bytes === '';
    }

    /**
     * Check if the byte string contains at least one byte.
     *
     * Returns true if the string length is greater than zero.
     *
     * @param  string  $bytes  The byte string to check.
     * @return bool  True if not empty, false otherwise.
     *
     * @example Bytes::isNotEmpty('A') // => true
     * @example Bytes::isNotEmpty('') // => false
     */
    public static function isNotEmpty(string $bytes): bool
    {
        return $bytes !== '';
    }
}
