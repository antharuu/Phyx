<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;
use ValueError;

/**
 * Character-code and hex conversion operations for {@see \Phyx\Str}.
 *
 * Single-character encoding (`fromCharCode` / `toCharCode`) is multibyte
 * aware through the {@see Encoding} enum; hex conversion (`toHex` /
 * `fromHex`) operates on raw bytes.
 */
trait HandleEncode
{
    /**
     * Encode `$value` as its lowercase hexadecimal representation.
     *
     * Wraps PHP's `bin2hex`. Each input byte becomes a pair of hex
     * characters (`0`–`9`, `a`–`f`). The empty string returns `''`.
     *
     * @param string $value The binary string to encode.
     * @return string        The hexadecimal representation.
     *
     * @example
     *   Str::toHex('abc');  // => '616263'
     *   Str::toHex("\xff"); // => 'ff'
     *
     * @see HandleEncode::fromHex The inverse operation.
     * @see https://www.php.net/manual/en/function.bin2hex.php PHP native equivalent
     */
    public static function toHex(string $value): string
    {
        return bin2hex($value);
    }

    /**
     * Decode the hexadecimal string `$value` back to its binary form.
     *
     * Wraps PHP's `hex2bin`. The input must contain an even number of
     * lowercase or uppercase hex digits (`[0-9a-fA-F]`); otherwise
     * `ValueError` is raised — Phyx surfaces the native failure as an
     * exception rather than returning the historical `false`.
     *
     * @param string $value The hexadecimal string to decode.
     * @return string        The decoded binary string.
     *
     * @throws ValueError When `$value` has odd length or non-hex characters.
     *
     * @example
     *   Str::fromHex('616263');  // => 'abc'
     *   Str::fromHex('');        // => ''
     *
     * @see HandleEncode::toHex The inverse operation.
     * @see https://www.php.net/manual/en/function.hex2bin.php PHP native equivalent
     */
    public static function fromHex(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $decoded = @hex2bin($value);

        if ($decoded === false) {
            throw new ValueError(
                'Str::fromHex(): Argument #1 ($value) must contain an even number of hexadecimal characters',
            );
        }

        return $decoded;
    }

    /**
     * Return the character at the given Unicode codepoint (or byte value).
     *
     * Multibyte-safe replacement for PHP's `chr` (which only works on
     * bytes 0–255). For UTF-8, codepoints up to `0x10FFFF` are valid;
     * invalid codepoints raise `ValueError`.
     *
     * @param int $codepoint The codepoint or byte value to encode.
     * @param Encoding $encoding Target encoding. Defaults to {@see Encoding::Utf8}.
     * @return string              A single-character string.
     *
     * @throws ValueError When `$codepoint` is negative or outside the encoding's valid range.
     *
     * @example
     *   Str::fromCharCode(65);    // => 'A'
     *   Str::fromCharCode(0x1F44B); // => '👋'
     *
     * @see HandleEncode::toCharCode The inverse operation.
     * @see https://www.php.net/manual/en/function.mb-chr.php PHP native equivalent
     */
    public static function fromCharCode(int $codepoint, Encoding $encoding = Encoding::Utf8): string
    {
        if ($codepoint < 0 || $codepoint > 0x10FFFF) {
            throw new ValueError(
                'Str::fromCharCode(): Argument #1 ($codepoint) "' . $codepoint . '" is not a valid Unicode codepoint',
            );
        }

        return mb_chr($codepoint, $encoding->value);
    }

    /**
     * Return the Unicode codepoint (or byte value) of the first character of `$value`.
     *
     * Multibyte-safe replacement for PHP's `ord` (which only works on
     * bytes 0–255). Reports the codepoint of the leading grapheme under
     * the chosen encoding.
     *
     * @param string $value The string whose first character to decode.
     *                            Must be non-empty.
     * @param Encoding $encoding The encoding of `$value`. Defaults to {@see Encoding::Utf8}.
     * @return int                The codepoint of the first character.
     *
     * @throws ValueError When `$value` is empty or starts with an invalid sequence.
     *
     * @example
     *   Str::toCharCode('A');   // => 65
     *   Str::toCharCode('👋hi'); // => 128075
     *
     * @see HandleEncode::fromCharCode The inverse operation.
     * @see https://www.php.net/manual/en/function.mb-ord.php PHP native equivalent
     */
    public static function toCharCode(string $value, Encoding $encoding = Encoding::Utf8): int
    {
        if ($value === '') {
            throw new ValueError(
                'Str::toCharCode(): Argument #1 ($value) cannot be empty',
            );
        }

        if (!mb_check_encoding($value, $encoding->value)) {
            throw new ValueError(
                'Str::toCharCode(): Argument #1 ($value) is not valid for encoding ' . $encoding->value,
            );
        }

        return mb_ord($value, $encoding->value);
    }
}
