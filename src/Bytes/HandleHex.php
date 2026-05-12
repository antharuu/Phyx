<?php

declare(strict_types=1);

namespace Phyx\Bytes;

use InvalidArgumentException;

/**
 * Hexadecimal encoding helpers for {@see \Phyx\Bytes}.
 */
trait HandleHex
{
    /**
     * Encode a raw byte string into a lowercase hexadecimal string.
     *
     * Converts each byte into its two-character hexadecimal representation.
     * The output will always have a length twice that of the input.
     *
     * @param  string  $bytes  The raw byte string to encode.
     * @return string  The lowercase hexadecimal representation.
     *
     * @example Bytes::toHex('Phyx') // => '50687978'
     * @example Bytes::toHex("\x00\xFF") // => '00ff'
     *
     * @see bin2hex
     */
    public static function toHex(string $bytes): string
    {
        return bin2hex($bytes);
    }

    /**
     * Decode a hexadecimal string into raw bytes.
     *
     * Reverses hexadecimal encoding to retrieve binary data. The input must
     * consist of an even number of hex digits.
     *
     * @param  string  $value  The hexadecimal string to decode.
     * @return string  The decoded raw byte string.
     *
     * @throws InvalidArgumentException When the input is not a valid hex string or has an odd length.
     *
     * @example Bytes::fromHex('50687978') // => 'Phyx'
     * @example Bytes::fromHex('00ff') // => "\x00\xFF"
     *
     * @see hex2bin
     * @see {@see tryFromHex}
     */
    public static function fromHex(string $value): string
    {
        $decoded = self::tryFromHex($value);

        if ($decoded === null) {
            throw new InvalidArgumentException('Invalid hexadecimal byte string.');
        }

        return $decoded;
    }

    /**
     * Attempt to decode a hexadecimal string into raw bytes.
     *
     * Returns the decoded bytes if the input is a valid even-length hex string,
     * or null if decoding fails.
     *
     * @param  string  $value  The hexadecimal string to decode.
     * @return string|null  The decoded raw byte string, or null on failure.
     *
     * @example Bytes::tryFromHex('50687978') // => 'Phyx'
     * @example Bytes::tryFromHex('invalid') // => null
     *
     * @see hex2bin
     * @see {@see fromHex}
     */
    public static function tryFromHex(string $value): ?string
    {
        if (! self::isHex($value)) {
            return null;
        }

        if ($value === '') {
            return '';
        }

        $decoded = hex2bin($value);

        return $decoded === false ? null : $decoded;
    }

    /**
     * Determine whether a string is a valid hexadecimal sequence.
     *
     * Checks if the string contains only hexadecimal digits (0-9, a-f) and has
     * an even length, making it suitable for decoding.
     *
     * @param  string  $value  The string to check.
     * @return bool  True if valid hex, false otherwise.
     *
     * @example Bytes::isHex('50687978') // => true
     * @example Bytes::isHex('506') // => false
     */
    public static function isHex(string $value): bool
    {
        return $value === '' || (strlen($value) % 2 === 0 && preg_match('/\A[0-9a-fA-F]+\z/', $value) === 1);
    }
}
