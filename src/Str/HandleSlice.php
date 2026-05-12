<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Encoding;

/**
 * Substring extraction operations for {@see \Phyx\Str}.
 *
 * Multibyte-safe replacements for PHP's `substr` family — offsets and
 * lengths are expressed in characters of the chosen {@see Encoding},
 * never in raw bytes.
 */
trait HandleSlice
{
    /**
     * Extract a portion of `$value` starting at `$start` for `$length` characters.
     *
     * Multibyte-aware counterpart of PHP's `substr()`, with the same offset
     * semantics: a negative `$start` counts from the end of the string,
     * a `null` `$length` extracts to the end of the string, and a negative
     * `$length` excludes that many characters from the end. All offsets and
     * lengths are in characters of the chosen {@see Encoding}, never bytes.
     *
     * Returns `''` when the requested slice is empty or lies outside the
     * string — never `false` (this is the main API improvement over
     * `substr`).
     *
     * @param string $value The source string.
     * @param int $start Character index where the slice begins.
     *                            Negative values count from the end.
     * @param  ?int $length Number of characters to extract. `null`
     *                            (default) extracts to the end of the string.
     *                            Negative values exclude that many characters
     *                            from the end.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             The extracted slice, or `''` if empty.
     *
     * @example
     *   Str::slice('Hello World', 6);        // => 'World'
     *   Str::slice('Hello World', 0, 5);     // => 'Hello'
     *   Str::slice('Hello World', -5);       // => 'World'
     *   Str::slice('Hello World', 0, -6);    // => 'Hello'
     *   Str::slice('café', 2);               // => 'fé'
     *   Str::slice('hello', 10);             // => ''
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.mb-substr.php PHP native equivalent
     */
    public static function slice(
        string   $value,
        int      $start,
        ?int     $length = null,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        return mb_substr($value, $start, $length, $encoding->value);
    }

    /**
     * Compare a slice of `$value` against `$other`.
     *
     * Multibyte-safe redesign of PHP's `substr_compare()`. The slice
     * starting at `$start` in `$value` (with the given `$length`, or to the
     * end of the string when `$length` is `null`) is compared against
     * `$other` — truncated to the same `$length` when one is provided.
     *
     * The return value is normalised to `-1`, `0` or `1`, never the raw
     * difference returned by `strcmp`. Case sensitivity is selected by the
     * {@see CaseSensitivity} enum.
     *
     * @param string $value The source string.
     * @param string $other The string to compare against.
     * @param int $start Character index in `$value` where the slice begins.
     *                                   Negative values count from the end.
     * @param  ?int $length Length of the slice to compare. `null`
     *                                   (default) compares to the end of both strings.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$other`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return int                       `-1` when the slice sorts before `$other`,
     *                                   `0` when they are equal, `1` when after.
     *
     * @example
     *   Str::sliceCompare('Hello World', 'World', 6);                // => 0
     *   Str::sliceCompare('Hello World', 'world', 6);                // => -1
     *   Str::sliceCompare('Hello World', 'world', 6,
     *       null, CaseSensitivity::Insensitive);                      // => 0
     *   Str::sliceCompare('abcDEF', 'abcxyz', 0, 3);                 // => 0
     *
     * @see CaseSensitivity
     * @see Encoding
     * @see https://www.php.net/manual/en/function.substr-compare.php PHP native equivalent
     */
    public static function sliceCompare(
        string          $value,
        string          $other,
        int             $start,
        ?int            $length = null,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): int
    {
        $extracted = mb_substr($value, $start, $length, $encoding->value);
        $otherSlice = $length === null
            ? $other
            : mb_substr($other, 0, $length, $encoding->value);

        $comparison = match ($case) {
            CaseSensitivity::Sensitive => strcmp($extracted, $otherSlice),
            CaseSensitivity::Insensitive => strcmp(
                mb_strtolower($extracted, $encoding->value),
                mb_strtolower($otherSlice, $encoding->value),
            ),
        };

        return $comparison <=> 0;
    }
}
