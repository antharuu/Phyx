<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Side;

/**
 * Border-trimming operations for {@see \Phyx\Str}.
 *
 * The single {@see HandleTrim::trim()} method replaces PHP's
 * `ltrim` / `rtrim` / `trim` triplet: which side(s) to trim is selected
 * through the {@see Side} enum. The implementation is multibyte-safe so
 * that non-ASCII characters can be used in the `$chars` set.
 */
trait HandleTrim
{
    /**
     * The default trim set: whitespace, tabs, line feeds, carriage returns,
     * NUL, and vertical tabs — identical to PHP's default `trim()` set.
     */
    private const DEFAULT_TRIM_CHARS = " \t\n\r\0\x0B";

    /**
     * Remove the leading and/or trailing occurrences of `$chars` from `$value`.
     *
     * Replaces PHP's three-way split between `ltrim`, `rtrim` and `trim`
     * by a single multibyte-safe method whose target side is selected by
     * the {@see Side} enum. The `$chars` parameter is a *set* of characters
     * (each character is matched independently), matching the semantics of
     * the native functions. Regex metacharacters in `$chars` are escaped
     * automatically, so `$chars` can safely contain `-`, `]`, `\`, `^`, etc.
     *
     * Returns the input unchanged when either `$value` or `$chars` is empty.
     *
     * @param string $value The string to trim.
     * @param string $chars The set of characters to remove. Defaults to
     *                      whitespace (space, tab, LF, CR, NUL, vertical tab).
     * @param Side $side Which side(s) to trim. Defaults to {@see Side::Both}.
     * @return string        A new trimmed string.
     *
     * @example
     *   Str::trim('  hello  ');                       // => 'hello'
     *   Str::trim('--abc--', '-');                    // => 'abc'
     *   Str::trim('--abc--', '-', Side::Start);       // => 'abc--'
     *   Str::trim('--abc--', '-', Side::End);         // => '--abc'
     *   Str::trim('éàhelloàé', 'éà');                 // => 'hello'
     *
     * @see Side
     * @see https://www.php.net/manual/en/function.trim.php PHP native equivalent (`trim`/`ltrim`/`rtrim`)
     */
    public static function trim(
        string $value,
        string $chars = self::DEFAULT_TRIM_CHARS,
        Side   $side = Side::Both,
    ): string
    {
        if ($value === '' || $chars === '') {
            return $value;
        }

        $class = preg_quote($chars, '/');

        $pattern = match ($side) {
            Side::Start => "/^[{$class}]+/u",
            Side::End => "/[{$class}]+\\z/u",
            Side::Both => "/^[{$class}]+|[{$class}]+\\z/u",
        };

        $result = preg_replace($pattern, '', $value);

        return $result ?? $value;
    }
}
