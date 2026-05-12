<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;
use Stringable;
use ValueError;

/**
 * Splitting and joining operations for {@see \Phyx\Str}.
 *
 * Methods that convert a string to a list (or vice-versa), parse
 * structured payloads (CSV, query strings, formatted text), and
 * tokenise on a character set.
 */
trait HandleSplit
{
    /**
     * Split `$value` into pieces around occurrences of `$separator`.
     *
     * Wraps PHP's `explode` with the Phyx convention `(value, ...)`. A
     * `$limit` greater than zero caps the number of returned pieces (the
     * trailing piece contains everything left); a negative `$limit` drops
     * that many trailing pieces; a `$limit` of `0` is normalised to `1`,
     * matching `explode`'s native behaviour.
     *
     * @param string $value The string to split.
     * @param string $separator The non-empty boundary substring.
     * @param int $limit Maximum number of pieces to return.
     *                           Defaults to no limit.
     * @return list<string>      The split pieces, never `false`.
     *
     * @throws ValueError When `$separator` is empty.
     *
     * @example
     *   Str::split('a,b,c,d', ',');         // => ['a', 'b', 'c', 'd']
     *   Str::split('a,b,c,d', ',', 2);      // => ['a', 'b,c,d']
     *   Str::split('a,b,c,d', ',', -1);     // => ['a', 'b', 'c']
     *
     * @see HandleSplit::join The inverse operation.
     * @see https://www.php.net/manual/en/function.explode.php PHP native equivalent
     */
    public static function split(string $value, string $separator, int $limit = PHP_INT_MAX): array
    {
        if ($separator === '') {
            throw new ValueError(
                'Str::split(): Argument #2 ($separator) cannot be empty',
            );
        }

        return explode($separator, $value, $limit);
    }

    /**
     * Split `$value` into chunks of `$length` characters.
     *
     * Multibyte-safe redesign of PHP's `str_split`. Counts characters in
     * the chosen encoding rather than raw bytes. The empty string returns
     * `[]` (instead of the native `['']`), which is more consistent with
     * "no characters → no chunks".
     *
     * @param string $value The string to split.
     * @param int $length Number of characters per chunk. Must be `>= 1`.
     *                            Defaults to `1`.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return list<string>          The split chunks.
     *
     * @throws ValueError When `$length` is less than `1`.
     *
     * @example
     *   Str::splitChars('Hello');     // => ['H', 'e', 'l', 'l', 'o']
     *   Str::splitChars('abcdef', 2); // => ['ab', 'cd', 'ef']
     *   Str::splitChars('café');      // => ['c', 'a', 'f', 'é']
     *   Str::splitChars('');          // => []
     *
     * @see https://www.php.net/manual/en/function.mb-str-split.php PHP native equivalent
     */
    public static function splitChars(string $value, int $length = 1, Encoding $encoding = Encoding::Utf8): array
    {
        if ($length < 1) {
            throw new ValueError(
                'Str::splitChars(): Argument #2 ($length) must be greater than 0',
            );
        }

        if ($value === '') {
            return [];
        }

        return mb_str_split($value, $length, $encoding->value);
    }

    /**
     * Join the elements of `$pieces` into a single string, separated by `$separator`.
     *
     * Wraps PHP's `implode` with the Phyx convention `(value, …)` — the
     * list being operated on is the first argument. Empty list yields `''`.
     *
     * @param list<string|int|float|Stringable> $pieces The items to concatenate.
     * @param string $separator The glue inserted between items.
     *                                                       Defaults to the empty string.
     * @return string                                        The joined string.
     *
     * @example
     *   Str::join(['a', 'b', 'c'], ',');  // => 'a,b,c'
     *   Str::join(['a', 'b', 'c']);       // => 'abc'
     *   Str::join([1, 2, 3], '-');        // => '1-2-3'
     *
     * @see HandleSplit::split The inverse operation.
     * @see https://www.php.net/manual/en/function.implode.php PHP native equivalent
     */
    public static function join(array $pieces, string $separator = ''): string
    {
        return implode($separator, $pieces);
    }

    /**
     * Parse a single CSV row from `$value`.
     *
     * Wraps PHP's `str_getcsv` with two stable improvements:
     * empty fields (which `str_getcsv` returns as `null`) are normalised
     * to `''` so the caller always works with strings; and the default
     * `$escape` is `''` (no escape character), matching the standard CSV
     * dialect and forward-compatible with PHP 8.4+ where the historical
     * backslash escape is deprecated.
     *
     * @param string $value The CSV row to parse.
     * @param string $separator The field separator. Defaults to `','`.
     * @param string $enclosure The field enclosure character. Defaults to `'"'`.
     * @param string $escape The escape character. Defaults to `''` (no escape).
     * @return list<string>      The parsed fields.
     *
     * @example
     *   Str::csv('a,b,c');              // => ['a', 'b', 'c']
     *   Str::csv('"hello, world",2');   // => ['hello, world', '2']
     *   Str::csv('a;b;c', ';');         // => ['a', 'b', 'c']
     *
     * @see https://www.php.net/manual/en/function.str-getcsv.php PHP native equivalent
     */
    public static function csv(
        string $value,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '',
    ): array
    {
        $row = str_getcsv($value, $separator, $enclosure, $escape);

        return array_map(static fn(?string $field): string => $field ?? '', $row);
    }

    /**
     * Parse `$value` against a `sscanf`-style `$format` and return the captured tokens.
     *
     * Wraps PHP's `sscanf` in its array form (without output references).
     * Returns `null` when the format does not match `$value` at all, so the
     * caller can distinguish "no match" from "matched but everything is
     * null" (impossible in this signature, but PHP-native `sscanf` can
     * sometimes return `null` for individual fields).
     *
     * @param string $value The string to parse.
     * @param string $format A `sscanf`-style format spec.
     * @return ?array<int, int|float|string|null>      The captured tokens or `null` on no match.
     *
     * @example
     *   Str::scan('hello 42 3.14', '%s %d %f');  // => ['hello', 42, 3.14]
     *   Str::scan('bad input', '%d');            // => [null]
     *
     * @see https://www.php.net/manual/en/function.sscanf.php PHP native equivalent
     */
    public static function scan(string $value, string $format): ?array
    {
        $result = sscanf($value, $format);

        return is_array($result) ? $result : null;
    }

    /**
     * Parse `$value` as a URL-encoded query string and return the decoded map.
     *
     * Safe redesign of PHP's `parse_str()`: instead of populating arbitrary
     * variables in the local scope (the unsafe `parse_str($s)` form,
     * removed in PHP 8.0), Phyx always returns the parsed map. Nested
     * structures (`a[b]=c`) are decoded into nested arrays.
     *
     * @param string $value The URL-encoded query string.
     * @return array<array-key, mixed>        The decoded map.
     *
     * @example
     *   Str::parseQuery('a=1&b=2');          // => ['a' => '1', 'b' => '2']
     *   Str::parseQuery('list[]=a&list[]=b'); // => ['list' => ['a', 'b']]
     *
     * @see https://www.php.net/manual/en/function.parse-str.php PHP native equivalent
     */
    public static function parseQuery(string $value): array
    {
        $result = [];
        parse_str($value, $result);

        return $result;
    }

    /**
     * Tokenise `$value` by splitting on any character from `$separators`.
     *
     * Redesign of PHP's stateful `strtok` into a stateless function that
     * returns every token at once. Each character of `$separators` is a
     * valid boundary; runs of separators are collapsed (no empty tokens
     * in the output).
     *
     * @param string $value The string to tokenise.
     * @param string $separators The set of boundary characters.
     * @return list<string>       The non-empty tokens.
     *
     * @example
     *   Str::tokenize('a b\tc\nd', " \t\n");  // => ['a', 'b', 'c', 'd']
     *   Str::tokenize('a/b//c', '/');         // => ['a', 'b', 'c']  (empty token dropped)
     *   Str::tokenize('', ',');               // => []
     *   Str::tokenize('abc', '');             // => []
     *
     * @see https://www.php.net/manual/en/function.strtok.php PHP native equivalent (stateful)
     */
    public static function tokenize(string $value, string $separators): array
    {
        if ($value === '' || $separators === '') {
            return [];
        }

        $pattern = '/[' . preg_quote($separators, '/') . ']+/';
        $tokens = preg_split($pattern, $value, -1, PREG_SPLIT_NO_EMPTY);

        return $tokens === false ? [] : $tokens;
    }
}
