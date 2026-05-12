<?php

declare(strict_types=1);

namespace Phyx\Str;

/**
 * Hashing and phonetic-key operations for {@see \Phyx\Str}.
 *
 * Thin Phyx-named wrappers over the native MD5/SHA-1/CRC-32 and
 * phonetic-key (`soundex` / `metaphone`) families. File variants return
 * `null` instead of `false` when the target cannot be read, so the API
 * stays predictable.
 */
trait HandleHash
{
    /**
     * Return the MD5 digest of `$value` as a lowercase hex string.
     *
     * Wraps PHP's `md5`. MD5 is unsuitable for password hashing and for
     * any security-critical use case — prefer `password_hash()` or
     * `hash('sha256', …)` for those. Phyx provides it for compatibility
     * with legacy formats (etags, fingerprints, etc.).
     *
     * @param string $value The string to digest.
     * @return string        A 32-character lowercase hex digest.
     *
     * @example
     *   Str::md5('hello'); // => '5d41402abc4b2a76b9719d911017c592'
     *
     * @see HandleHash::md5File
     * @see https://www.php.net/manual/en/function.md5.php PHP native equivalent
     */
    public static function md5(string $value): string
    {
        return md5($value);
    }

    /**
     * Return the MD5 digest of the file at `$path`.
     *
     * Wraps PHP's `md5_file`. Returns `null` when the file cannot be read
     * (missing, permission denied, …) — never `false` — so the result is
     * consistent with the string variant.
     *
     * @param string $path Absolute or relative path to the file.
     * @return ?string       The 32-character lowercase hex digest, or
     *                       `null` when the file cannot be read.
     *
     * @example
     *   Str::md5File('/etc/hostname'); // => 'xxxxxxxx...'
     *   Str::md5File('/no/such/file'); // => null
     *
     * @see https://www.php.net/manual/en/function.md5-file.php PHP native equivalent
     */
    public static function md5File(string $path): ?string
    {
        $digest = @md5_file($path);

        return $digest === false ? null : $digest;
    }

    /**
     * Return the SHA-1 digest of `$value` as a lowercase hex string.
     *
     * Wraps PHP's `sha1`. Like {@see HandleHash::md5()}, this is not
     * suitable for password hashing.
     *
     * @param string $value The string to digest.
     * @return string        A 40-character lowercase hex digest.
     *
     * @example
     *   Str::sha1('hello'); // => 'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d'
     *
     * @see https://www.php.net/manual/en/function.sha1.php PHP native equivalent
     */
    public static function sha1(string $value): string
    {
        return sha1($value);
    }

    /**
     * Return the SHA-1 digest of the file at `$path`.
     *
     * Wraps PHP's `sha1_file`. Returns `null` when the file cannot be read.
     *
     * @param string $path Absolute or relative path to the file.
     * @return ?string       The 40-character lowercase hex digest, or
     *                       `null` when the file cannot be read.
     *
     * @example
     *   Str::sha1File('/etc/hostname'); // => 'xxxxxxxx...'
     *   Str::sha1File('/no/such/file'); // => null
     *
     * @see https://www.php.net/manual/en/function.sha1-file.php PHP native equivalent
     */
    public static function sha1File(string $path): ?string
    {
        $digest = @sha1_file($path);

        return $digest === false ? null : $digest;
    }

    /**
     * Return the 32-bit CRC checksum of `$value` as an integer.
     *
     * Wraps PHP's `crc32`. The result is always returned as a non-negative
     * integer (PHP 7+ behaviour on 64-bit platforms; the legacy
     * 32-bit-signed result is normalised).
     *
     * @param string $value The string to digest.
     * @return int           The 32-bit CRC checksum.
     *
     * @example
     *   Str::crc32('hello'); // => 907060870
     *
     * @see https://www.php.net/manual/en/function.crc32.php PHP native equivalent
     */
    public static function crc32(string $value): int
    {
        return crc32($value);
    }

    /**
     * Return the one-way `crypt` hash of `$value` using `$salt`.
     *
     * Thin wrapper over PHP's `crypt`. The salt format determines the
     * algorithm used (`'$2y$10$…'` for bcrypt, `'$6$…'` for SHA-512crypt, …).
     * Phyx requires the salt to be explicitly provided — there is no
     * implicit weak-default behaviour as in some legacy PHP versions.
     *
     * @param string $value The string to hash.
     * @param string $salt A salt in any format `crypt()` understands.
     * @return string        The hashed string (algorithm-specific format).
     *
     * @example
     *   Str::crypt('secret', '$2y$10$abcdefghijklmnopqrstuv');
     *
     * @see https://www.php.net/manual/en/function.crypt.php PHP native equivalent
     */
    public static function crypt(string $value, string $salt): string
    {
        return crypt($value, $salt);
    }

    /**
     * Return the Soundex phonetic key of `$value`.
     *
     * Wraps PHP's `soundex`. The key is a 4-character ASCII code that
     * approximates how the input sounds in English (`'Robert'` and
     * `'Rupert'` share the same key `R163`).
     *
     * @param string $value The string to phoneticise.
     * @return string        The 4-character Soundex key.
     *
     * @example
     *   Str::soundex('Robert');  // => 'R163'
     *   Str::soundex('Rupert');  // => 'R163'
     *   Str::soundex('Tymczak'); // => 'T522'
     *
     * @see HandleHash::metaphone A more accurate alternative.
     * @see https://www.php.net/manual/en/function.soundex.php PHP native equivalent
     */
    public static function soundex(string $value): string
    {
        return soundex($value);
    }

    /**
     * Return the Metaphone phonetic key of `$value`.
     *
     * Wraps PHP's `metaphone`. The Metaphone algorithm is more accurate
     * than Soundex for English phonetics and produces a variable-length
     * ASCII key.
     *
     * @param string $value The string to phoneticise.
     * @param int $phonemes Maximum length of the returned key.
     *                          `0` (default) means no limit.
     * @return string           The Metaphone key.
     *
     * @example
     *   Str::metaphone('Thompson'); // => 'TMSN'
     *   Str::metaphone('xylophone'); // => 'SLFN'
     *   Str::metaphone('Thompson', 3); // => 'TMS'
     *
     * @see HandleHash::soundex
     * @see https://www.php.net/manual/en/function.metaphone.php PHP native equivalent
     */
    public static function metaphone(string $value, int $phonemes = 0): string
    {
        return metaphone($value, $phonemes);
    }
}
