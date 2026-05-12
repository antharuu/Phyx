<?php

declare(strict_types=1);

namespace Phyx\Bytes;

/**
 * Byte-based extraction helpers for {@see \Phyx\Bytes}.
 */
trait HandleSlice
{
    /**
     * Extract a portion of a byte string.
     *
     * Returns the sub-string starting at the given offset. If length is provided,
     * it limits the number of bytes returned.
     *
     * @param  string    $bytes   The raw byte string.
     * @param  int       $offset  The starting position (can be negative).
     * @param  int|null  $length  The number of bytes to extract. Defaults to null (rest of string).
     * @return string  The extracted byte slice.
     *
     * @example Bytes::slice('Phyx', 1, 2) // => 'hy'
     * @example Bytes::slice('Phyx', -2) // => 'yx'
     *
     * @see substr
     */
    public static function slice(string $bytes, int $offset, ?int $length = null): string
    {
        return $length === null
            ? substr($bytes, $offset)
            : substr($bytes, $offset, $length);
    }

    /**
     * Extract a specific number of bytes from the start.
     *
     * Returns a slice of the string beginning at the first byte. If the requested
     * length exceeds the string length, the entire string is returned.
     *
     * @param  string  $bytes   The raw byte string.
     * @param  int     $length  The number of bytes to take.
     * @return string  The leading byte slice.
     *
     * @example Bytes::take('Phyx', 2) // => 'Ph'
     * @example Bytes::take('Phyx', 10) // => 'Phyx'
     *
     * @see {@see slice}
     */
    public static function take(string $bytes, int $length): string
    {
        if ($length <= 0) {
            return '';
        }

        return self::slice($bytes, 0, $length);
    }

    /**
     * Extract a specific number of bytes from the end.
     *
     * Returns a slice of the string ending at the last byte. If the requested
     * length exceeds the string length, the entire string is returned.
     *
     * @param  string  $bytes   The raw byte string.
     * @param  int     $length  The number of bytes to take.
     * @return string  The trailing byte slice.
     *
     * @example Bytes::takeLast('Phyx', 2) // => 'yx'
     * @example Bytes::takeLast('Phyx', 10) // => 'Phyx'
     *
     * @see {@see slice}
     */
    public static function takeLast(string $bytes, int $length): string
    {
        if ($length <= 0) {
            return '';
        }

        if ($length >= strlen($bytes)) {
            return $bytes;
        }

        return self::slice($bytes, -$length);
    }

    /**
     * Get a single byte at a specific position.
     *
     * Returns the byte at the given offset, or null if the offset is out of
     * bounds.
     *
     * @param  string  $bytes   The raw byte string.
     * @param  int     $offset  The position to retrieve (can be negative).
     * @return string|null  The single byte, or null if invalid offset.
     *
     * @example Bytes::byteAt('Phyx', 0) // => 'P'
     * @example Bytes::byteAt('Phyx', 5) // => null
     *
     * @see substr
     */
    public static function byteAt(string $bytes, int $offset): ?string
    {
        $byte = substr($bytes, $offset, 1);

        return $byte === '' ? null : $byte;
    }
}
