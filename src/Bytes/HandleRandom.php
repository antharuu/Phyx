<?php

declare(strict_types=1);

namespace Phyx\Bytes;

use Phyx\Enums\Checksum;
use ValueError;

/**
 * Random bytes and lightweight checksum helpers for {@see \Phyx\Bytes}.
 */
trait HandleRandom
{
    /**
     * Generate a cryptographically secure random byte string.
     *
     * Uses a CSPRNG to produce high-entropy random data suitable for keys,
     * salts, and nonces.
     *
     * @param  int  $length  The number of bytes to generate.
     * @return string  A string of random bytes.
     *
     * @throws ValueError When the requested length is less than 0.
     * @throws \Exception When a source of randomness cannot be found.
     *
     * @example Bytes::random(16) // => (16 random bytes)
     * @example Bytes::random(0) // => ''
     *
     * @see random_bytes
     */
    public static function random(int $length): string
    {
        if ($length < 0) {
            throw new ValueError('Random byte length must be greater than or equal to 0.');
        }

        if ($length === 0) {
            return '';
        }

        return random_bytes($length);
    }

    /**
     * Calculate a checksum for a byte string.
     *
     * Computes a non-cryptographic hash or cyclic redundancy check to detect
     * accidental data changes.
     *
     * @param  string    $bytes      The byte string to hash.
     * @param  Checksum  $algorithm  The algorithm to use. Defaults to {@see Checksum::Crc32}.
     * @return int|string  The checksum value (integer for CRC32, string for Adler32).
     *
     * @example Bytes::checksum('Phyx') // => 3788094936
     * @example Bytes::checksum('Phyx', Checksum::Adler32) // => '043e019a'
     *
     * @see crc32
     * @see hash
     */
    public static function checksum(string $bytes, Checksum $algorithm = Checksum::Crc32): int|string
    {
        return match ($algorithm) {
            Checksum::Crc32 => self::crc32($bytes),
            Checksum::Adler32 => hash('adler32', $bytes),
        };
    }

    /**
     * Compute the CRC32 checksum as an unsigned integer.
     *
     * Provides a consistent unsigned 32-bit integer regardless of the
     * platform's integer size.
     *
     * @param  string  $bytes  The byte string to hash.
     * @return int  The unsigned CRC32 checksum.
     *
     * @example Bytes::crc32('Phyx') // => 3788094936
     *
     * @see crc32
     */
    public static function crc32(string $bytes): int
    {
        return (int) sprintf('%u', crc32($bytes));
    }
}
