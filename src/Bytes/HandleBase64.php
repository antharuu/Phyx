<?php

declare(strict_types=1);

namespace Phyx\Bytes;

use InvalidArgumentException;
use Phyx\Enums\PaddingMode;

/**
 * Base64 and base64url helpers for {@see \Phyx\Bytes}.
 */
trait HandleBase64
{
    /**
     * Encode a raw byte string into standard base64.
     *
     * Converts binary data into an ASCII string using the standard RFC 4648
     * alphabet. This process is binary-safe and does not modify the underlying
     * bytes.
     *
     * @param  string  $bytes  The raw byte string to encode.
     * @return string  The base64-encoded representation.
     *
     * @example Bytes::toBase64('Phyx') // => 'UGh5eA=='
     * @example Bytes::toBase64("\x00\xFF") // => 'AP8='
     *
     * @see base64_encode
     */
    public static function toBase64(string $bytes): string
    {
        return base64_encode($bytes);
    }

    /**
     * Decode a standard base64-encoded string into raw bytes.
     *
     * Reverses the base64 encoding to retrieve the original binary data. In
     * strict mode, it enforces RFC 4648 compliance, including correct padding
     * and the absence of non-alphabet characters.
     *
     * @param  string       $value  The base64 string to decode.
     * @param  PaddingMode  $mode   The validation mode for padding and characters. Defaults to {@see PaddingMode::Strict}.
     * @return string       The decoded raw byte string.
     *
     * @throws InvalidArgumentException When the input is malformed or violates the {@see PaddingMode}.
     *
     * @example Bytes::fromBase64('UGh5eA==') // => 'Phyx'
     * @example Bytes::fromBase64('AP8=') // => "\x00\xFF"
     *
     * @see base64_decode
     * @see {@see tryFromBase64}
     */
    public static function fromBase64(string $value, PaddingMode $mode = PaddingMode::Strict): string
    {
        $decoded = self::tryFromBase64($value, $mode);

        if ($decoded === null) {
            throw new InvalidArgumentException('Invalid base64 byte string.');
        }

        return $decoded;
    }

    /**
     * Attempt to decode a standard base64-encoded string into raw bytes.
     *
     * Returns the decoded bytes if the input is valid according to the specified
     * mode, or null if decoding fails. Useful for handling untrusted input without
     * exceptions.
     *
     * @param  string       $value  The base64 string to decode.
     * @param  PaddingMode  $mode   The validation mode for padding and characters. Defaults to {@see PaddingMode::Strict}.
     * @return string|null  The decoded raw byte string, or null on failure.
     *
     * @example Bytes::tryFromBase64('UGh5eA==') // => 'Phyx'
     * @example Bytes::tryFromBase64('!!!') // => null
     *
     * @see base64_decode
     * @see {@see fromBase64}
     */
    public static function tryFromBase64(string $value, PaddingMode $mode = PaddingMode::Strict): ?string
    {
        $normalised = self::normaliseBase64($value, $mode);

        if ($normalised === null) {
            return null;
        }

        if ($normalised === '') {
            return '';
        }

        return self::decodeNormalisedBase64($normalised);
    }

    private static function normaliseBase64(string $value, PaddingMode $mode): ?string
    {
        if ($mode === PaddingMode::Strict) {
            return self::isStrictBase64($value) ? $value : null;
        }

        return self::normaliseLenientBase64($value);
    }

    private static function normaliseLenientBase64(string $value): ?string
    {
        $normalised = preg_replace('/\s+/', '', $value) ?? $value;

        if (preg_match('/\A[A-Za-z0-9+\/]*={0,2}\z/', $normalised) !== 1) {
            return null;
        }

        $normalised = rtrim($normalised, '=');
        $remainder = strlen($normalised) % 4;

        if ($remainder === 1) {
            return null;
        }

        if ($remainder > 0) {
            $normalised .= str_repeat('=', 4 - $remainder);
        }

        return $normalised;
    }

    private static function decodeNormalisedBase64(string $normalised): ?string
    {
        $decoded = base64_decode($normalised, true);

        return $decoded === false ? null : $decoded;
    }

    /**
     * Encode a raw byte string into unpadded base64url.
     *
     * Produces a URL-safe base64 string by replacing '+' with '-' and '/' with '_'
     * and stripping trailing '=' padding, as defined in RFC 4648.
     *
     * @param  string  $bytes  The raw byte string to encode.
     * @return string  The base64url-encoded representation.
     *
     * @example Bytes::toBase64Url("\xFB\xFF\xBF") // => '-_-_'
     * @example Bytes::toBase64Url('Phyx') // => 'UGh5eA'
     *
     * @see {@see toBase64}
     */
    public static function toBase64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Decode an unpadded base64url-encoded string into raw bytes.
     *
     * Validates and decodes a URL-safe base64 string. It handles the absence of
     * padding by internally restoring it before decoding.
     *
     * @param  string  $value  The base64url string to decode.
     * @return string  The decoded raw byte string.
     *
     * @throws InvalidArgumentException When the input contains invalid base64url characters.
     *
     * @example Bytes::fromBase64Url('-_-_') // => "\xFB\xFF\xBF"
     * @example Bytes::fromBase64Url('UGh5eA') // => 'Phyx'
     *
     * @see {@see fromBase64}
     */
    public static function fromBase64Url(string $value): string
    {
        if ($value !== '' && preg_match('/\A[A-Za-z0-9_-]+\z/', $value) !== 1) {
            throw new InvalidArgumentException('Invalid base64url byte string.');
        }

        $standard = strtr($value, '-_', '+/');
        $remainder = strlen($standard) % 4;

        if ($remainder === 1) {
            throw new InvalidArgumentException('Invalid base64url byte string.');
        }

        if ($remainder > 0) {
            $standard .= str_repeat('=', 4 - $remainder);
        }

        return self::fromBase64($standard, PaddingMode::Strict);
    }

    private static function isStrictBase64(string $value): bool
    {
        if (strlen($value) % 4 !== 0) {
            return false;
        }

        return preg_match('/\A(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?\z/', $value) === 1;
    }
}
