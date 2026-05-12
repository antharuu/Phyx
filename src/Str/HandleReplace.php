<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Encoding;

/**
 * Substring replacement operations for {@see \Phyx\Str}.
 *
 * Methods that produce a new string by substituting some part(s) of the
 * input. Case sensitivity is selected through the {@see CaseSensitivity}
 * enum; encoding through {@see Encoding}.
 */
trait HandleReplace
{
    /**
     * Replace every occurrence of `$search` with `$replacement` inside `$value`.
     *
     * Multibyte-safe redesign of PHP's `str_replace` / `str_ireplace`.
     * The empty needle is a no-op (returns the input unchanged) instead of
     * silently doing nothing the wrong way. Case-insensitive matching is
     * implemented through a `preg_replace_callback` with the `iu` flag so
     * that accented letters fold correctly under the chosen encoding.
     *
     * @param string $value The source string.
     * @param string $search The substring to replace. Empty needle = no-op.
     * @param string $replacement The substring to insert.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                      {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of all inputs.
     *                                      Defaults to {@see Encoding::Utf8}.
     * @return string                       A new string with every match replaced.
     *
     * @example
     *   Str::replace('Hello World', 'World', 'Phyx');                              // => 'Hello Phyx'
     *   Str::replace('hello hello', 'hello', 'hi');                                // => 'hi hi'
     *   Str::replace('Hello World', 'hello', 'hi',
     *       CaseSensitivity::Insensitive);                                         // => 'hi World'
     *
     * @see HandleReplace::replaceMany For multiple substitutions at once.
     * @see https://www.php.net/manual/en/function.str-replace.php PHP native equivalent
     */
    public static function replace(
        string          $value,
        string          $search,
        string          $replacement,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): string
    {
        if ($search === '') {
            return $value;
        }

        return match ($case) {
            CaseSensitivity::Sensitive => str_replace($search, $replacement, $value),
            CaseSensitivity::Insensitive => preg_replace_callback(
                '/' . preg_quote($search, '/') . '/iu',
                static fn(): string => $replacement,
                $value,
            ) ?? $value,
        };
    }

    /**
     * Apply a map of substitutions to `$value`, in dictionary order.
     *
     * Multibyte-safe redesign of `str_replace` / `str_ireplace` with array
     * arguments. The `$replacements` parameter is a flat map
     * `search => replacement`, which is far less error-prone than the
     * native API that takes two parallel arrays and silently misbehaves
     * when their indices drift apart.
     *
     * The empty map is a no-op (returns the input unchanged).
     *
     * @param string $value The source string.
     * @param array<string, string> $replacements Map of `search => replacement`.
     * @param CaseSensitivity $case Matching mode. Defaults to
     *                                             {@see CaseSensitivity::Sensitive}.
     * @param Encoding $encoding The character encoding of all inputs.
     *                                             Defaults to {@see Encoding::Utf8}.
     * @return string                              A new string with every match replaced.
     *
     * @example
     *   Str::replaceMany('Hello World', ['Hello' => 'Hi', 'World' => 'Phyx']);
     *   // => 'Hi Phyx'
     *
     * @see HandleReplace::replace For a single substitution.
     */
    public static function replaceMany(
        string          $value,
        array           $replacements,
        CaseSensitivity $case = CaseSensitivity::Sensitive,
        Encoding        $encoding = Encoding::Utf8,
    ): string
    {
        if ($replacements === []) {
            return $value;
        }

        $searches = array_keys($replacements);
        $values = array_values($replacements);

        return match ($case) {
            CaseSensitivity::Sensitive => str_replace($searches, $values, $value),
            CaseSensitivity::Insensitive => str_ireplace($searches, $values, $value),
        };
    }

    /**
     * Replace a slice of `$value` between `$start` and `$start + $length`
     * with `$replacement`.
     *
     * Multibyte-safe redesign of PHP's `substr_replace`. Offsets and lengths
     * are expressed in characters of the chosen encoding. A negative `$start`
     * counts from the end. A `null` `$length` extends the slice to the end of
     * the string. A negative `$length` excludes that many characters from the end.
     *
     * @param string $value The source string.
     * @param string $replacement The substring to insert.
     * @param int $start Character index where the slice begins.
     *                               Negative values count from the end.
     * @param  ?int $length Length of the slice to replace. `null`
     *                               replaces to the end of the string;
     *                               negative values exclude that many
     *                               characters from the end.
     * @param Encoding $encoding The character encoding of `$value` and
     *                               `$replacement`. Defaults to {@see Encoding::Utf8}.
     * @return string                A new string with the slice replaced.
     *
     * @example
     *   Str::replaceSlice('Hello World', 'Phyx', 6);          // => 'Hello Phyx'
     *   Str::replaceSlice('Hello World', '!', 5, 6);          // => 'Hello!'
     *   Str::replaceSlice('Hello World', '*', -5, 5);         // => 'Hello *'
     *
     * @see https://www.php.net/manual/en/function.substr-replace.php PHP native equivalent
     */
    public static function replaceSlice(
        string   $value,
        string   $replacement,
        int      $start,
        ?int     $length = null,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        $total = mb_strlen($value, $encoding->value);

        $start = $start < 0
            ? max(0, $total + $start)
            : min($start, $total);

        if ($length === null) {
            $length = $total - $start;
        } elseif ($length < 0) {
            $length = max(0, $total - $start + $length);
        } else {
            $length = min($length, $total - $start);
        }

        $before = mb_substr($value, 0, $start, $encoding->value);
        $after = mb_substr($value, $start + $length, null, $encoding->value);

        return $before . $replacement . $after;
    }

    /**
     * Translate each occurrence of a key in `$mapping` to its value inside `$value`.
     *
     * Wraps PHP's `strtr()` with the array form: each key is searched in
     * `$value` and replaced by its associated value. Unlike a sequence of
     * `replace()` calls, every position in `$value` is rewritten at most
     * once, so the substitutions cannot cascade onto each other.
     *
     * @param string $value The source string.
     * @param array<string, string> $mapping Map of `from => to` substrings.
     * @return string                         A new translated string.
     *
     * @example
     *   Str::translate('hello world', ['hello' => 'bonjour', 'world' => 'monde']);
     *   // => 'bonjour monde'
     *   Str::translate('ab', ['a' => 'b', 'b' => 'a']); // => 'ba'  (no cascade)
     *
     * @see https://www.php.net/manual/en/function.strtr.php PHP native equivalent
     */
    public static function translate(string $value, array $mapping): string
    {
        if ($mapping === []) {
            return $value;
        }

        return strtr($value, $mapping);
    }

    /**
     * Apply the ROT13 substitution cipher to `$value`.
     *
     * Letters in the ASCII range are shifted by 13 positions; every other
     * character (digits, punctuation, multibyte letters, …) is left
     * untouched. ROT13 is its own inverse, so `rot13(rot13($x)) === $x`.
     *
     * @param string $value The string to obfuscate.
     * @return string        The ROT13-transformed string.
     *
     * @example
     *   Str::rot13('Hello');  // => 'Uryyb'
     *   Str::rot13('Uryyb');  // => 'Hello'
     *
     * @see https://www.php.net/manual/en/function.str-rot13.php PHP native equivalent
     */
    public static function rot13(string $value): string
    {
        return str_rot13($value);
    }
}
