<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;
use Phyx\Enums\Side;
use Phyx\Polyfills\AlphaIncrement;
use ValueError;

/**
 * Structural transformations for {@see \Phyx\Str}.
 *
 * Operations that reshape a string while keeping its `string` type:
 * reversing, repeating, shuffling, padding, wrapping, chunking,
 * and lexicographic increment/decrement.
 */
trait HandleShape
{
    /**
     * Return `$value` with its characters in reverse order.
     *
     * Multibyte-safe redesign of PHP's `strrev` (which is byte-based and
     * corrupts non-ASCII UTF-8 strings). The empty string returns `''`.
     *
     * @param string $value The string to reverse.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new reversed string.
     *
     * @example
     *   Str::reverse('Hello');  // => 'olleH'
     *   Str::reverse('café');   // => 'éfac'
     *   Str::reverse('');       // => ''
     *
     * @see https://www.php.net/manual/en/function.strrev.php PHP native equivalent (byte-based)
     */
    public static function reverse(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        if ($value === '') {
            return '';
        }

        return implode('', array_reverse(mb_str_split($value, 1, $encoding->value)));
    }

    /**
     * Return `$value` repeated `$times` times.
     *
     * Thin wrapper over `str_repeat`. A `$times` of `0` returns `''`;
     * negative values raise `ValueError` (delegated to the native function).
     *
     * @param string $value The string to repeat.
     * @param int $times How many times to repeat. Must be `>= 0`.
     * @return string        The concatenation of `$times` copies of `$value`.
     *
     * @throws ValueError When `$times` is negative.
     *
     * @example
     *   Str::repeat('ab', 3);  // => 'ababab'
     *   Str::repeat('x', 0);   // => ''
     *
     * @see https://www.php.net/manual/en/function.str-repeat.php PHP native equivalent
     */
    public static function repeat(string $value, int $times): string
    {
        return str_repeat($value, $times);
    }

    /**
     * Return `$value` with its characters in pseudo-random order.
     *
     * Multibyte-safe redesign of PHP's `str_shuffle` (which is byte-based
     * and corrupts non-ASCII UTF-8 strings). Uses PHP's default
     * pseudo-random number generator; not cryptographically secure.
     *
     * @param string $value The string to shuffle.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new string containing the same characters
     *                            in a (probably) different order.
     *
     * @example
     *   Str::shuffle('abcdef'); // => e.g. 'bdcaef' (varies)
     *
     * @see https://www.php.net/manual/en/function.str-shuffle.php PHP native equivalent (byte-based)
     */
    public static function shuffle(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        if ($value === '') {
            return '';
        }

        $chars = mb_str_split($value, 1, $encoding->value);
        shuffle($chars);

        return implode('', $chars);
    }

    /**
     * Pad `$value` with `$with` until it reaches `$length` characters.
     *
     * Multibyte-safe redesign of PHP's `str_pad` (which counts bytes, not
     * characters, and uses three constants instead of a typed enum).
     * Which side(s) are padded is selected by the {@see Side} enum.
     *
     * If `$value` is already at least `$length` characters long, it is
     * returned unchanged. If `$with` is empty, the input is returned
     * unchanged as well (instead of triggering an infinite loop).
     *
     * @param string $value The string to pad.
     * @param int $length The total desired character length.
     * @param string $with The padding string. Defaults to a single space.
     * @param Side $side Which side(s) to pad. Defaults to {@see Side::End}.
     * @param Encoding $encoding The character encoding of `$value` and `$with`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return string             A new padded string, exactly `$length` characters
     *                            long (or the input itself when no padding is needed).
     *
     * @example
     *   Str::pad('5', 3, '0', Side::Start);    // => '005'
     *   Str::pad('hi', 5);                     // => 'hi   '
     *   Str::pad('hi', 6, '-', Side::Both);    // => '--hi--'
     *   Str::pad('café', 6, '.', Side::End);   // => 'café..'
     *
     * @see Side
     * @see https://www.php.net/manual/en/function.str-pad.php PHP native equivalent (byte-based)
     */
    public static function pad(
        string   $value,
        int      $length,
        string   $with = ' ',
        Side     $side = Side::End,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        $valueLength = mb_strlen($value, $encoding->value);

        if ($valueLength >= $length || $with === '') {
            return $value;
        }

        $padding = $length - $valueLength;
        $withLength = mb_strlen($with, $encoding->value);

        return match ($side) {
            Side::End => $value . self::buildFiller($with, $padding, $withLength, $encoding),
            Side::Start => self::buildFiller($with, $padding, $withLength, $encoding) . $value,
            Side::Both => self::buildFiller($with, intdiv($padding, 2), $withLength, $encoding)
                . $value
                . self::buildFiller($with, $padding - intdiv($padding, 2), $withLength, $encoding),
        };
    }

    /**
     * Build a padding filler of exactly `$length` characters by repeating `$with`.
     *
     * @param string $with The pattern to repeat.
     * @param int $length The desired filler length in characters.
     * @param int $withLength Pre-computed length of `$with` in characters.
     * @param Encoding $encoding Encoding of `$with`.
     * @return string               A filler string exactly `$length` characters long.
     */
    private static function buildFiller(
        string   $with,
        int      $length,
        int      $withLength,
        Encoding $encoding,
    ): string
    {
        if ($length <= 0) {
            return '';
        }

        $repeats = intdiv($length, $withLength) + 1;

        return mb_substr(str_repeat($with, $repeats), 0, $length, $encoding->value);
    }

    /**
     * Soft-wrap `$value` so that no line exceeds `$width` characters.
     *
     * Multibyte-safe redesign of PHP's `wordwrap`. Words longer than
     * `$width` are kept on their own line unless `$cutLongWords` is `true`,
     * in which case they are forcibly split. Line breaks are inserted using
     * `$break` (`"\n"` by default).
     *
     * @param string $value The string to wrap.
     * @param int $width Maximum characters per line. Must be `> 0`.
     * @param string $break The line-break sequence to insert.
     *                                Defaults to `"\n"`.
     * @param bool $cutLongWords When `true`, split words that exceed `$width`.
     *                                Defaults to `false`.
     * @param Encoding $encoding The character encoding of `$value`.
     *                                Defaults to {@see Encoding::Utf8}.
     * @return string                 The wrapped string.
     *
     * @example
     *   Str::wrap('The quick brown fox', 10);
     *   // => "The quick\nbrown fox"
     *
     * @see https://www.php.net/manual/en/function.wordwrap.php PHP native equivalent
     */
    public static function wrap(
        string   $value,
        int      $width,
        string   $break = "\n",
        bool     $cutLongWords = false,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        if ($width < 1) {
            throw new ValueError(
                'Str::wrap(): Argument #2 ($width) must be greater than 0',
            );
        }

        if ($value === '') {
            return '';
        }

        $lines = explode("\n", $value);
        $wrapped = [];

        foreach ($lines as $line) {
            $wrapped[] = self::wrapLine($line, $width, $break, $cutLongWords, $encoding);
        }

        return implode("\n", $wrapped);
    }

    /**
     * Wrap a single logical line.
     *
     * @param string $line The line to wrap (no embedded newlines).
     * @param int<1, max> $width Maximum characters per output line.
     * @param string $break The line-break sequence.
     * @param bool $cutLongWords Whether to split words that exceed `$width`.
     * @param Encoding $encoding Encoding of `$line`.
     */
    private static function wrapLine(
        string   $line,
        int      $width,
        string   $break,
        bool     $cutLongWords,
        Encoding $encoding,
    ): string
    {
        if ($line === '') {
            return '';
        }

        $words = explode(' ', $line);
        $output = '';
        $currentLength = 0;

        foreach ($words as $index => $word) {
            $wordLength = mb_strlen($word, $encoding->value);

            if ($cutLongWords && $wordLength > $width) {
                if ($currentLength > 0) {
                    $output .= $break;
                    $currentLength = 0;
                }
                $chunks = mb_str_split($word, $width, $encoding->value);
                $output .= implode($break, $chunks);
                $currentLength = mb_strlen($chunks[count($chunks) - 1], $encoding->value);
                continue;
            }

            $separatorLength = $index === 0 ? 0 : 1;
            $candidateLength = $currentLength + $separatorLength + $wordLength;

            if ($currentLength === 0) {
                $output .= $word;
                $currentLength = $wordLength;
            } elseif ($candidateLength <= $width) {
                $output .= ' ' . $word;
                $currentLength = $candidateLength;
            } else {
                $output .= $break . $word;
                $currentLength = $wordLength;
            }
        }

        return $output;
    }

    /**
     * Split `$value` into chunks of `$length` characters joined by `$separator`.
     *
     * Multibyte-safe redesign of PHP's `chunk_split`. The separator is also
     * appended after the final chunk (matching the native behaviour) so the
     * output is consistent for streaming use cases (e.g. base64 wrapping).
     *
     * @param string $value The string to chunk.
     * @param int $length Number of characters per chunk. Defaults to `76`
     *                             (the historical chunk size for MIME-encoded data).
     *                             Must be `>= 1`.
     * @param string $separator The separator inserted between (and after) chunks.
     *                             Defaults to `"\r\n"`.
     * @param Encoding $encoding The character encoding of `$value`.
     *                             Defaults to {@see Encoding::Utf8}.
     * @return string              The chunked string.
     *
     * @throws ValueError When `$length` is less than `1`.
     *
     * @example
     *   Str::chunk('abcdefghij', 3, '-');  // => 'abc-def-ghi-j-'
     *   Str::chunk('', 3);                 // => ''
     *
     * @see https://www.php.net/manual/en/function.chunk-split.php PHP native equivalent (byte-based)
     */
    public static function chunk(
        string   $value,
        int      $length = 76,
        string   $separator = "\r\n",
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        if ($length < 1) {
            throw new ValueError('Argument #2 ($length) must be greater than 0');
        }

        if ($value === '') {
            return '';
        }

        return implode($separator, mb_str_split($value, $length, $encoding->value)) . $separator;
    }

    /**
     * Return the alphanumeric increment of `$value`.
     *
     * Mirrors PHP 8.3's `str_increment`: the input must be a non-empty
     * alphanumeric ASCII string; mixed or punctuated inputs raise
     * `ValueError`. Carries propagate naturally — `'Az'` becomes `'Ba'`,
     * `'Zz'` becomes `'AAa'`.
     *
     * Uses the native function when available (PHP 8.3+); falls back to
     * {@see AlphaIncrement::increment()} on earlier versions.
     *
     * @param string $value A non-empty alphanumeric ASCII string.
     * @return string        The incremented string.
     *
     * @throws ValueError When `$value` is empty or contains non-alphanumeric characters.
     *
     * @example
     *   Str::increment('a');   // => 'b'
     *   Str::increment('z');   // => 'aa'
     *   Str::increment('Az');  // => 'Ba'
     *   Str::increment('Zz');  // => 'AAa'
     *   Str::increment('A9');  // => 'B0'
     *
     * @see HandleShape::decrement The inverse operation.
     * @see https://www.php.net/manual/en/function.str-increment.php PHP native equivalent (PHP 8.3+)
     */
    public static function increment(string $value): string
    {
        if ($value === '') {
            throw new ValueError(
                'Str::increment(): Argument #1 ($value) cannot be empty',
            );
        }

        if (function_exists('str_increment')) {
            return str_increment($value);
        }

        // @codeCoverageIgnoreStart
        return AlphaIncrement::increment($value);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return the alphanumeric decrement of `$value`.
     *
     * Mirrors PHP 8.3's `str_decrement`: the input must be a non-empty
     * alphanumeric ASCII string strictly greater than `'a'` / `'A'` /
     * `'0'`, with no leading `'0'`; otherwise `ValueError` is raised.
     *
     * Uses the native function when available (PHP 8.3+); falls back to
     * {@see AlphaIncrement::decrement()} on earlier versions.
     *
     * @param string $value A non-empty alphanumeric ASCII string greater than the minimum.
     * @return string        The decremented string.
     *
     * @throws ValueError When `$value` is empty, non-alphanumeric, starts with `'0'`,
     *                     or already at the minimum representable value.
     *
     * @example
     *   Str::decrement('b');   // => 'a'
     *   Str::decrement('aa');  // => 'z'
     *   Str::decrement('Ba');  // => 'Az'
     *
     * @see HandleShape::increment The inverse operation.
     * @see https://www.php.net/manual/en/function.str-decrement.php PHP native equivalent (PHP 8.3+)
     */
    public static function decrement(string $value): string
    {
        if ($value === '') {
            throw new ValueError(
                'Str::decrement(): Argument #1 ($value) cannot be empty',
            );
        }

        if (function_exists('str_decrement')) {
            return str_decrement($value);
        }

        // @codeCoverageIgnoreStart
        return AlphaIncrement::decrement($value);
        // @codeCoverageIgnoreEnd
    }
}
