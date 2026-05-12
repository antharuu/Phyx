<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;

/**
 * Letter-case operations for {@see \Phyx\Str}.
 *
 * Every method is multibyte-safe and returns a fresh string — the input
 * is never mutated. The character encoding is selected through the
 * {@see Encoding} enum and defaults to {@see Encoding::Utf8}.
 */
trait HandleCase
{
    /**
     * Return the given string with every letter folded to lowercase.
     *
     * Multibyte-safe (`mb_strtolower`): non-ASCII letters such as `'É'`
     * or `'Ñ'` are folded correctly. Empty strings round-trip unchanged.
     *
     * @param string $value The string to fold.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new string where every letter is lowercase.
     *
     * @example
     *   Str::lower('Hello WORLD'); // => 'hello world'
     *   Str::lower('ÉCOLE');       // => 'école'
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.mb-strtolower.php PHP native equivalent
     */
    public static function lower(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        return mb_strtolower($value, $encoding->value);
    }

    /**
     * Return the given string with every letter folded to uppercase.
     *
     * Multibyte-safe (`mb_strtoupper`): non-ASCII letters such as `'é'`
     * or `'ñ'` are folded correctly. Empty strings round-trip unchanged.
     *
     * @param string $value The string to fold.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new string where every letter is uppercase.
     *
     * @example
     *   Str::upper('Hello world'); // => 'HELLO WORLD'
     *   Str::upper('école');       // => 'ÉCOLE'
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.mb-strtoupper.php PHP native equivalent
     */
    public static function upper(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        return mb_strtoupper($value, $encoding->value);
    }

    /**
     * Return the given string with **only the first** character uppercased.
     *
     * Multibyte-safe: the leading grapheme is folded via `mb_strtoupper`
     * and the rest of the string is left untouched (including its existing
     * case). The empty string returns `''`. Replaces PHP's `ucfirst()`,
     * whose ASCII-only behaviour is unsafe for UTF-8 input.
     *
     * @param string $value The string to capitalise.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new string with its first character uppercased.
     *
     * @example
     *   Str::capitalize('hello world'); // => 'Hello world'
     *   Str::capitalize('école');       // => 'École'
     *   Str::capitalize('');            // => ''
     *
     * @see HandleCase::capitalizeWords To capitalise every word.
     * @see Encoding
     * @see https://www.php.net/manual/en/function.ucfirst.php PHP native equivalent
     */
    public static function capitalize(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        if ($value === '') {
            return '';
        }

        $first = mb_substr($value, 0, 1, $encoding->value);
        $rest = mb_substr($value, 1, null, $encoding->value);

        return mb_strtoupper($first, $encoding->value) . $rest;
    }

    /**
     * Return the given string with **only the first** character lowercased.
     *
     * Multibyte-safe counterpart of PHP's `lcfirst()`. The leading grapheme
     * is folded via `mb_strtolower`; the rest of the string is left untouched.
     * The empty string returns `''`.
     *
     * @param string $value The string to decapitalise.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new string with its first character lowercased.
     *
     * @example
     *   Str::decapitalize('Hello World'); // => 'hello World'
     *   Str::decapitalize('École');       // => 'école'
     *   Str::decapitalize('');            // => ''
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.lcfirst.php PHP native equivalent
     */
    public static function decapitalize(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        if ($value === '') {
            return '';
        }

        $first = mb_substr($value, 0, 1, $encoding->value);
        $rest = mb_substr($value, 1, null, $encoding->value);

        return mb_strtolower($first, $encoding->value) . $rest;
    }

    /**
     * Return the given string with the first character of **every word** uppercased.
     *
     * A word is any maximal run of letters separated by whitespace, in the
     * sense of `mb_convert_case` with `MB_CASE_TITLE`. The rest of each
     * word is folded to lowercase, so the result is normalised regardless
     * of the input casing — this is the standard title-case behaviour and
     * matches what users intuitively expect from a "capitalise words" call.
     *
     * @param string $value The string to title-case.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new title-cased string.
     *
     * @example
     *   Str::capitalizeWords('hello WORLD');     // => 'Hello World'
     *   Str::capitalizeWords('école polytechnique'); // => 'École Polytechnique'
     *
     * @see HandleCase::capitalize To capitalise only the first character.
     * @see Encoding
     * @see https://www.php.net/manual/en/function.mb-convert-case.php PHP native equivalent
     */
    public static function capitalizeWords(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, $encoding->value);
    }
}
