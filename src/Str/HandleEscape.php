<?php

declare(strict_types=1);

namespace Phyx\Str;

/**
 * Slash/quote escaping operations for {@see \Phyx\Str}.
 *
 * Thin Phyx-named wrappers around `addslashes` / `addcslashes` /
 * `stripslashes` / `stripcslashes` / `quotemeta`. ASCII-oriented:
 * these functions operate on byte sequences and are typically used for
 * legacy serialisation formats (SQL string literals, C-style escapes,
 * etc.).
 */
trait HandleEscape
{
    /**
     * Escape `'`, `"`, `\` and the NUL byte with a backslash.
     *
     * Wraps PHP's `addslashes`. Useful when producing SQL string literals
     * for engines without modern parameterised queries (which should be
     * preferred whenever possible).
     *
     * @param string $value The string to escape.
     * @return string        The escaped string.
     *
     * @example
     *   Str::addSlashes("It's a \"test\"");  // => "It\\'s a \\\"test\\\""
     *
     * @see HandleEscape::stripSlashes The inverse operation.
     * @see https://www.php.net/manual/en/function.addslashes.php PHP native equivalent
     */
    public static function addSlashes(string $value): string
    {
        return addslashes($value);
    }

    /**
     * Escape every character of `$value` that appears in `$charset` using C-style escapes.
     *
     * Wraps PHP's `addcslashes`. The `$charset` parameter is a character
     * range expression compatible with the native function (`'A..Z'`,
     * `'a..f0..9'`, …).
     *
     * @param string $value The string to escape.
     * @param string $charset The set of characters to escape (range expression).
     * @return string          The escaped string.
     *
     * @example
     *   Str::addCSlashes('Hello', 'A..Z');  // => '\\Hello'
     *   Str::addCSlashes('abc', 'a..c');    // => '\\a\\b\\c'
     *
     * @see HandleEscape::stripCSlashes The inverse operation.
     * @see https://www.php.net/manual/en/function.addcslashes.php PHP native equivalent
     */
    public static function addCSlashes(string $value, string $charset): string
    {
        return addcslashes($value, $charset);
    }

    /**
     * Un-escape backslash sequences previously produced by `addSlashes`.
     *
     * Wraps PHP's `stripslashes`. Performs a single de-escape pass; only
     * meaningful when `$value` has been produced (directly or indirectly)
     * by `addslashes`.
     *
     * @param string $value The string to un-escape.
     * @return string        The un-escaped string.
     *
     * @example
     *   Str::stripSlashes("It\\'s a \\\"test\\\"");  // => "It's a \"test\""
     *
     * @see HandleEscape::addSlashes The inverse operation.
     * @see https://www.php.net/manual/en/function.stripslashes.php PHP native equivalent
     */
    public static function stripSlashes(string $value): string
    {
        return stripslashes($value);
    }

    /**
     * Decode C-style escape sequences previously produced by `addCSlashes`.
     *
     * Wraps PHP's `stripcslashes`. Decodes `\n`, `\r`, `\t`, `\xHH`,
     * octal and the literal forms produced by `addcslashes`.
     *
     * @param string $value The string to decode.
     * @return string        The decoded string.
     *
     * @example
     *   Str::stripCSlashes('\\a\\b\\c'); // => 'abc'
     *   Str::stripCSlashes('line\\n');   // => "line\n"
     *
     * @see HandleEscape::addCSlashes The inverse operation.
     * @see https://www.php.net/manual/en/function.stripcslashes.php PHP native equivalent
     */
    public static function stripCSlashes(string $value): string
    {
        return stripcslashes($value);
    }

    /**
     * Backslash-escape the regex metacharacters `. \ + * ? [ ^ ] $ ( )` in `$value`.
     *
     * Wraps PHP's `quotemeta`. Useful for embedding user input inside a
     * simple regex without using PHP's wider `preg_quote()` (which escapes
     * a larger set of metacharacters).
     *
     * @param string $value The string to escape.
     * @return string        The escaped string.
     *
     * @example
     *   Str::quoteMeta('1+2.5*3'); // => '1\\+2\\.5\\*3'
     *
     * @see https://www.php.net/manual/en/function.quotemeta.php PHP native equivalent
     */
    public static function quoteMeta(string $value): string
    {
        return quotemeta($value);
    }
}
