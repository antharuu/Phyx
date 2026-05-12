<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Encoding;
use Phyx\Enums\Ordering;

/**
 * String comparison and similarity operations for {@see \Phyx\Str}.
 *
 * Collapses PHP's strcmp/strcasecmp/strncmp/strncasecmp/strnatcmp/
 * strnatcasecmp/strcoll family into a single {@see HandleCompare::compare()}
 * method controlled by two orthogonal enums: {@see CaseSensitivity} and
 * {@see Ordering}.
 */
trait HandleCompare
{
    /**
     * Compare the first `$length` characters of two strings.
     *
     * Multibyte-safe counterpart of PHP's `strncmp` / `strncasecmp`. Each
     * input is truncated to `$length` characters of the chosen encoding
     * before comparison. A non-positive `$length` short-circuits to `0`
     * (two empty prefixes are equal).
     *
     * @param string $a The first string.
     * @param string $b The second string.
     * @param int $length Number of leading characters to compare.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$a` and `$b`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return int                       `-1`, `0` or `1`.
     *
     * @example
     *   Str::comparePrefix('abcdef', 'abcxyz', 3);              // => 0
     *   Str::comparePrefix('abcdef', 'abdxyz', 3);              // => -1
     *   Str::comparePrefix('Abc', 'abc', 3, CaseSensitivity::Insensitive); // => 0
     *
     * @see https://www.php.net/manual/en/function.strncmp.php PHP native equivalent (byte-based)
     */
    public static function comparePrefix(
        string          $a,
        string          $b,
        int             $length,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): int
    {
        if ($length <= 0) {
            return 0;
        }

        $prefixA = mb_substr($a, 0, $length, $encoding->value);
        $prefixB = mb_substr($b, 0, $length, $encoding->value);

        return self::compare($prefixA, $prefixB, $case, Ordering::Binary, $encoding);
    }

    /**
     * Compare two strings and return their lexicographic order as `-1`, `0` or `1`.
     *
     * Replaces six native functions with a single signature whose behaviour
     * is selected by two orthogonal enums:
     *
     *  - {@see CaseSensitivity} switches between `strcmp` and `strcasecmp`
     *    family (case-insensitive matching folds both inputs through
     *    `mb_strtolower` first so the comparison stays multibyte-correct).
     *  - {@see Ordering} switches between byte-wise (`strcmp`), natural
     *    (`strnatcmp`) and locale-aware (`strcoll`) algorithms.
     *
     * The return value is normalised to `-1`, `0` or `1`, never the raw
     * difference reported by the underlying native function — so callers
     * can safely use `===` against expected values without worrying about
     * platform-specific magnitudes.
     *
     * @param string $a The first string.
     * @param string $b The second string.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Ordering $ordering Comparison algorithm. Defaults to
     *                                   {@see Ordering::Binary}.
     * @param Encoding $encoding The character encoding of `$a` and `$b`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return int                       `-1` when `$a` sorts before `$b`,
     *                                   `0` when they are equal, `1` when after.
     *
     * @example
     *   Str::compare('apple', 'banana');                          // => -1
     *   Str::compare('Apple', 'apple');                           // => -1  (uppercase sorts first in ASCII)
     *   Str::compare('Apple', 'apple', CaseSensitivity::Insensitive); // => 0
     *   Str::compare('item2', 'item10');                          // => 1   (binary)
     *   Str::compare('item2', 'item10', ordering: Ordering::Natural); // => -1
     *
     * @see CaseSensitivity
     * @see Ordering
     * @see Encoding
     */
    public static function compare(
        string          $a,
        string          $b,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Ordering        $ordering = Ordering::Binary,
        Encoding        $encoding = Encoding::Utf8,
    ): int
    {
        if ($case === CaseSensitivity::Insensitive) {
            $a = mb_strtolower($a, $encoding->value);
            $b = mb_strtolower($b, $encoding->value);
        }

        $cmp = match ($ordering) {
            Ordering::Binary => strcmp($a, $b),
            Ordering::Natural => strnatcmp($a, $b),
            Ordering::Locale => strcoll($a, $b),
        };

        return $cmp <=> 0;
    }

    /**
     * Compute how similar two strings are.
     *
     * Wraps PHP's `similar_text` and returns both the raw count of matched
     * characters and the similarity ratio as a float in `[0.0, 100.0]`,
     * instead of writing the percentage to a by-reference argument.
     *
     * @param string $a The first string.
     * @param string $b The second string.
     * @return array{matches: int, percent: float} A struct-like map with
     *                  `matches` (number of matched characters) and
     *                  `percent` (similarity ratio, `[0.0, 100.0]`).
     *
     * @example
     *   Str::similarity('Hello World', 'Hello Phyx');
     *   // => ['matches' => 7, 'percent' => 66.66666...]
     *
     * @see https://www.php.net/manual/en/function.similar-text.php PHP native equivalent
     */
    public static function similarity(string $a, string $b): array
    {
        $percent = 0.0;
        $matches = similar_text($a, $b, $percent);

        return ['matches' => $matches, 'percent' => $percent];
    }

    /**
     * Compute the Levenshtein edit distance between two strings.
     *
     * Wraps PHP's `levenshtein()` byte-based algorithm. Older builds
     * (pre-PHP 8.4 official, before backports) returned `-1` for inputs
     * longer than 255 bytes; modern builds removed that limit and always
     * return a non-negative `int`. Phyx forwards the native return as-is.
     *
     * @param string $a The first string.
     * @param string $b The second string.
     * @return int       The minimum number of single-character edits
     *                   (insertions, deletions, substitutions) required
     *                   to turn `$a` into `$b`. On legacy PHP builds with
     *                   the 255-byte cap, may return `-1` instead.
     *
     * @example
     *   Str::distance('kitten', 'sitting'); // => 3
     *   Str::distance('hello', 'hello');    // => 0
     *
     * @see https://www.php.net/manual/en/function.levenshtein.php PHP native equivalent
     */
    public static function distance(string $a, string $b): int
    {
        return levenshtein($a, $b);
    }
}
