<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Encoding;

/**
 * Presence-and-position operations for {@see \Phyx\Str}.
 *
 * Methods that inspect a string to test for, locate, or extract around
 * a substring. The case-matching mode is selected through the
 * {@see CaseSensitivity} enum and the character encoding through the
 * {@see Encoding} enum.
 *
 * Every "not found" outcome returns `null` (for positions / extractions)
 * or `false` (for predicates) — no `-1` sentinels, no `false`/`int` unions.
 */
trait HandleSearch
{
    /**
     * Determine whether `$value` contains the substring `$search`.
     *
     * Returns `true` for the empty needle, matching PHP's `str_contains`
     * semantics. When {@see CaseSensitivity::Insensitive} is passed, the
     * multibyte-aware `mb_stripos` is used so that accented letters fold
     * consistently with their case mapping under the chosen encoding.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to look for. May be empty.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return bool                      `true` when `$search` is found in `$value`,
     *                                   `false` otherwise.
     *
     * @example
     *   Str::contains('Hello World', 'world');                                // false
     *   Str::contains('Hello World', 'world', CaseSensitivity::Insensitive);  // true
     *   Str::contains('café', 'fé');                                          // true
     *
     * @see CaseSensitivity
     * @see Encoding
     * @see https://www.php.net/manual/en/function.str-contains.php PHP native equivalent
     */
    public static function contains(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): bool
    {
        if ($search === '') {
            return true;
        }

        return match ($case) {
            CaseSensitivity::Sensitive => str_contains($value, $search),
            CaseSensitivity::Insensitive => mb_stripos($value, $search, 0, $encoding->value) !== false,
        };
    }

    /**
     * Determine whether `$value` begins with the substring `$search`.
     *
     * Returns `true` for the empty needle, matching PHP's `str_starts_with`
     * semantics. Case-insensitive matching folds both ends through
     * `mb_strtolower` so the comparison stays multibyte-correct.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to look for. May be empty.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return bool                      `true` when `$value` begins with `$search`.
     *
     * @example
     *   Str::startsWith('Hello World', 'Hello');                               // true
     *   Str::startsWith('Hello World', 'hello');                               // false
     *   Str::startsWith('Hello World', 'hello', CaseSensitivity::Insensitive); // true
     *
     * @see https://www.php.net/manual/en/function.str-starts-with.php PHP native equivalent
     */
    public static function startsWith(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): bool
    {
        if ($search === '') {
            return true;
        }

        return match ($case) {
            CaseSensitivity::Sensitive => str_starts_with($value, $search),
            CaseSensitivity::Insensitive => str_starts_with(
                mb_strtolower($value, $encoding->value),
                mb_strtolower($search, $encoding->value),
            ),
        };
    }

    /**
     * Determine whether `$value` ends with the substring `$search`.
     *
     * Returns `true` for the empty needle, matching PHP's `str_ends_with`
     * semantics. Case-insensitive matching folds both ends through
     * `mb_strtolower` so the comparison stays multibyte-correct.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to look for. May be empty.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return bool                      `true` when `$value` ends with `$search`.
     *
     * @example
     *   Str::endsWith('Hello World', 'World');                               // true
     *   Str::endsWith('Hello World', 'WORLD');                               // false
     *   Str::endsWith('Hello World', 'WORLD', CaseSensitivity::Insensitive); // true
     *
     * @see https://www.php.net/manual/en/function.str-ends-with.php PHP native equivalent
     */
    public static function endsWith(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): bool
    {
        if ($search === '') {
            return true;
        }

        return match ($case) {
            CaseSensitivity::Sensitive => str_ends_with($value, $search),
            CaseSensitivity::Insensitive => str_ends_with(
                mb_strtolower($value, $encoding->value),
                mb_strtolower($search, $encoding->value),
            ),
        };
    }

    /**
     * Return the portion of `$value` **after** the first occurrence of `$search`.
     *
     * The returned slice excludes the needle itself. Returns `null` when
     * `$search` is not present, so callers can distinguish "not found"
     * from "found, but the remainder is empty" (which returns `''`).
     *
     * @param string $value The source string.
     * @param string $search The needle whose first occurrence delimits the slice.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?string                   Portion of `$value` after the first `$search`,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::after('https://phyx.dev/docs', '://');  // => 'phyx.dev/docs'
     *   Str::after('hello', 'xyz');                  // => null
     *
     * @see HandleSearch::afterLast To slice after the last occurrence.
     * @see HandleSearch::before
     */
    public static function after(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?string
    {
        $position = self::indexOf($value, $search, $case, $encoding);

        if ($position === null) {
            return null;
        }

        return mb_substr($value, $position + mb_strlen($search, $encoding->value), null, $encoding->value);
    }

    /**
     * Return the character index of the first occurrence of `$search` in `$value`.
     *
     * Multibyte-safe replacement for `strpos` / `stripos`. The index is in
     * characters (not bytes) of the chosen encoding. Returns `null` when
     * `$search` is not found — never `false` and never `-1`.
     *
     * The empty needle is treated as not found and returns `null`, which
     * differs from PHP's `mb_strpos` (whose behaviour on empty needle has
     * varied across versions). This is a deliberate API improvement.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to look for.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?int                      Character index of the first match,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::indexOf('Hello World', 'World');                              // 6
     *   Str::indexOf('Hello World', 'world');                              // null
     *   Str::indexOf('Hello World', 'world', CaseSensitivity::Insensitive); // 6
     *   Str::indexOf('café', 'fé');                                        // 2
     *
     * @see HandleSearch::lastIndexOf To search from the end.
     * @see https://www.php.net/manual/en/function.mb-strpos.php PHP native equivalent
     */
    public static function indexOf(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?int
    {
        if ($search === '') {
            return null;
        }

        $position = match ($case) {
            CaseSensitivity::Sensitive => mb_strpos($value, $search, 0, $encoding->value),
            CaseSensitivity::Insensitive => mb_stripos($value, $search, 0, $encoding->value),
        };

        return $position === false ? null : $position;
    }

    /**
     * Return the portion of `$value` **after** the *last* occurrence of `$search`.
     *
     * @param string $value The source string.
     * @param string $search The needle whose last occurrence delimits the slice.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?string                   Portion of `$value` after the last `$search`,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::afterLast('a/b/c/d', '/');   // => 'd'
     *   Str::afterLast('hello', 'xyz');   // => null
     *
     * @see HandleSearch::after
     */
    public static function afterLast(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?string
    {
        $position = self::lastIndexOf($value, $search, $case, $encoding);

        if ($position === null) {
            return null;
        }

        return mb_substr($value, $position + mb_strlen($search, $encoding->value), null, $encoding->value);
    }

    /**
     * Return the character index of the **last** occurrence of `$search` in `$value`.
     *
     * Multibyte-safe replacement for `strrpos` / `strripos`. The index is
     * in characters (not bytes) of the chosen encoding. Returns `null`
     * when `$search` is not found — never `false` and never `-1`.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to look for.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?int                      Character index of the last match,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::lastIndexOf('Hello World World', 'World');  // 12
     *   Str::lastIndexOf('Hello World', 'xyz');          // null
     *
     * @see HandleSearch::indexOf To search from the start.
     * @see https://www.php.net/manual/en/function.mb-strrpos.php PHP native equivalent
     */
    public static function lastIndexOf(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?int
    {
        if ($search === '') {
            return null;
        }

        $position = match ($case) {
            CaseSensitivity::Sensitive => mb_strrpos($value, $search, 0, $encoding->value),
            CaseSensitivity::Insensitive => mb_strripos($value, $search, 0, $encoding->value),
        };

        return $position === false ? null : $position;
    }

    /**
     * Return the portion of `$value` **before** the first occurrence of `$search`.
     *
     * Returns `null` when `$search` is not present. When `$value` starts
     * with `$search`, returns `''`.
     *
     * @param string $value The source string.
     * @param string $search The needle whose first occurrence delimits the slice.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?string                   Portion of `$value` before the first `$search`,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::before('https://phyx.dev/docs', '://');  // => 'https'
     *   Str::before('a/b/c/d', '/');                   // => 'a'
     *   Str::before('hello', 'xyz');                   // => null
     *
     * @see HandleSearch::beforeLast
     * @see HandleSearch::after
     */
    public static function before(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?string
    {
        $position = self::indexOf($value, $search, $case, $encoding);

        if ($position === null) {
            return null;
        }

        return mb_substr($value, 0, $position, $encoding->value);
    }

    /**
     * Return the portion of `$value` **before** the *last* occurrence of `$search`.
     *
     * @param string $value The source string.
     * @param string $search The needle whose last occurrence delimits the slice.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?string                   Portion of `$value` before the last `$search`,
     *                                   or `null` when not found.
     *
     * @example
     *   Str::beforeLast('a/b/c/d', '/');  // => 'a/b/c'
     *   Str::beforeLast('hello', 'xyz');  // => null
     *
     * @see HandleSearch::before
     */
    public static function beforeLast(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?string
    {
        $position = self::lastIndexOf($value, $search, $case, $encoding);

        if ($position === null) {
            return null;
        }

        return mb_substr($value, 0, $position, $encoding->value);
    }

    /**
     * Return the portion of `$value` starting at the first occurrence of `$search`.
     *
     * Multibyte-safe redesign of PHP's `strstr` / `stristr`. Includes the
     * needle in the returned slice (unlike {@see HandleSearch::after()}
     * which excludes it). Returns `null` when not found — never `false`.
     *
     * @param string $value The source string.
     * @param string $search The needle whose first occurrence delimits the slice.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                   {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                                   Defaults to {@see Encoding::Utf8}.
     * @return ?string                   Portion of `$value` from the first `$search`
     *                                   inclusive, or `null` when not found.
     *
     * @example
     *   Str::firstOf('hello@phyx.dev', '@');  // => '@phyx.dev'
     *   Str::firstOf('hello', 'xyz');         // => null
     *
     * @see HandleSearch::after
     * @see https://www.php.net/manual/en/function.mb-strstr.php PHP native equivalent
     */
    public static function firstOf(
        string          $value,
        string          $search,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): ?string
    {
        if ($search === '') {
            return null;
        }

        $result = match ($case) {
            CaseSensitivity::Sensitive => mb_strstr($value, $search, false, $encoding->value),
            CaseSensitivity::Insensitive => mb_stristr($value, $search, false, $encoding->value),
        };

        return $result === false ? null : $result;
    }

    /**
     * Return the number of **words** in `$value`.
     *
     * Multibyte-safe redesign of PHP's `str_word_count()` (whose default
     * behaviour is locale- and ASCII-centric). Phyx defines a word as any
     * maximal run of Unicode letters (optionally containing apostrophes
     * between letters, so contractions like `don't` count as one word).
     * The empty string returns `0`.
     *
     * @param string $value The string to inspect.
     * @return int           The number of words found.
     *
     * @example
     *   Str::wordCount('Hello brave new world');  // => 4
     *   Str::wordCount("don't be afraid");        // => 3
     *   Str::wordCount('école polytechnique');    // => 2
     *   Str::wordCount('');                       // => 0
     *
     * @see https://www.php.net/manual/en/function.str-word-count.php PHP native equivalent
     */
    public static function wordCount(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        $count = preg_match_all("/\p{L}+(?:'\p{L}+)*/u", $value);

        return $count === false ? 0 : $count;
    }

    /**
     * Return the number of non-overlapping occurrences of `$search` in `$value`.
     *
     * Multibyte-safe wrapper around `mb_substr_count`. Returns `0` for the
     * empty needle (instead of the native ValueError) so the API is
     * predictable: counting nothing is zero, not an exception.
     *
     * @param string $value The string to inspect.
     * @param string $search The substring to count.
     * @param Encoding $encoding The character encoding of `$value` and `$search`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return int                The number of non-overlapping occurrences.
     *
     * @example
     *   Str::occurrences('banana', 'a');   // => 3
     *   Str::occurrences('hello', '');     // => 0
     *   Str::occurrences('aaaa', 'aa');    // => 2  (non-overlapping)
     *
     * @see https://www.php.net/manual/en/function.mb-substr-count.php PHP native equivalent
     */
    public static function occurrences(
        string   $value,
        string   $search,
        Encoding $encoding = Encoding::Utf8,
    ): int
    {
        if ($search === '') {
            return 0;
        }

        return mb_substr_count($value, $search, $encoding->value);
    }

    /**
     * Return the length of the initial segment of `$value` made of characters from `$chars`.
     *
     * Multibyte-safe redesign of PHP's `strspn()`. The walk starts at the
     * beginning of `$value` and stops at the first character that is not
     * in the `$chars` set. The length is reported in characters of the
     * chosen encoding.
     *
     * @param string $value The string to walk.
     * @param string $chars The set of allowed characters.
     * @param Encoding $encoding The character encoding of `$value` and `$chars`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return int                The length, in characters, of the initial allowed run.
     *
     * @example
     *   Str::span('aaabbb', 'a');      // => 3
     *   Str::span('123abc', '0123456789'); // => 3
     *   Str::span('xyz', 'abc');       // => 0
     *
     * @see HandleSearch::cspan Inverse of this method.
     * @see https://www.php.net/manual/en/function.strspn.php PHP native equivalent (byte-based)
     */
    public static function span(
        string   $value,
        string   $chars,
        Encoding $encoding = Encoding::Utf8,
    ): int
    {
        return self::spanInternal($value, $chars, true, $encoding);
    }

    /**
     * Shared walker for {@see HandleSearch::span()} and {@see HandleSearch::cspan()}.
     *
     * @param string $value The string to walk.
     * @param string $chars The reference set of characters.
     * @param bool $matchSet `true` to stop on the first char not in `$chars` (span),
     *                             `false` to stop on the first char in `$chars` (cspan).
     * @param Encoding $encoding The character encoding of `$value` and `$chars`.
     * @return int                 Length of the initial qualifying run.
     */
    private static function spanInternal(
        string   $value,
        string   $chars,
        bool     $matchSet,
        Encoding $encoding,
    ): int
    {
        if ($value === '' || $chars === '') {
            return $matchSet ? 0 : mb_strlen($value, $encoding->value);
        }

        $charSet = [];
        $setLength = mb_strlen($chars, $encoding->value);
        for ($i = 0; $i < $setLength; $i++) {
            $charSet[mb_substr($chars, $i, 1, $encoding->value)] = true;
        }

        $valueLength = mb_strlen($value, $encoding->value);
        for ($i = 0; $i < $valueLength; $i++) {
            $char = mb_substr($value, $i, 1, $encoding->value);
            $inSet = isset($charSet[$char]);

            if ($matchSet ? !$inSet : $inSet) {
                return $i;
            }
        }

        return $valueLength;
    }

    /**
     * Return the length of the initial segment of `$value` made of characters **not** in `$chars`.
     *
     * Multibyte-safe redesign of PHP's `strcspn()`. The walk starts at the
     * beginning of `$value` and stops at the first character that *is* in
     * the `$chars` set.
     *
     * @param string $value The string to walk.
     * @param string $chars The set of forbidden characters.
     * @param Encoding $encoding The character encoding of `$value` and `$chars`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return int                The length, in characters, of the initial forbidden-free run.
     *
     * @example
     *   Str::cspan('abc123', '0123456789'); // => 3
     *   Str::cspan('123abc', '0123456789'); // => 0
     *   Str::cspan('hello world', ' ');     // => 5
     *
     * @see HandleSearch::span Inverse of this method.
     * @see https://www.php.net/manual/en/function.strcspn.php PHP native equivalent (byte-based)
     */
    public static function cspan(
        string   $value,
        string   $chars,
        Encoding $encoding = Encoding::Utf8,
    ): int
    {
        return self::spanInternal($value, $chars, false, $encoding);
    }
}
