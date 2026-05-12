<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;

/**
 * Size and inventory operations for {@see \Phyx\Str}.
 *
 * Both methods are multibyte-safe: they count *characters* (codepoints in
 * the chosen {@see Encoding}), not raw bytes. Defaults to UTF-8.
 */
trait HandleLength
{
    /**
     * Return the number of characters in `$value` under the given encoding.
     *
     * Multibyte-aware counterpart of PHP's `strlen()` — `'café'` is reported
     * as `4`, not `5` (which is the byte length under UTF-8). The empty
     * string always returns `0`.
     *
     * @param string $value The string to measure.
     * @param Encoding $encoding The character encoding of `$value`.
     *                            Defaults to {@see Encoding::Utf8}.
     * @return int                The number of characters in `$value`.
     *
     * @example
     *   Str::length('hello'); // => 5
     *   Str::length('café');  // => 4
     *   Str::length('');      // => 0
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.mb-strlen.php PHP native equivalent
     */
    public static function length(string $value, Encoding $encoding = Encoding::Utf8): int
    {
        return mb_strlen($value, $encoding->value);
    }

    /**
     * Return the per-character occurrence map of `$value`.
     *
     * Multibyte-safe redesign of PHP's `count_chars($value, 1)` — that
     * native function only works on bytes (0–255) and is therefore unsafe
     * for non-ASCII input. Phyx returns a dictionary keyed by the actual
     * character (under the chosen encoding), so accented and non-ASCII
     * characters are counted correctly. Characters that do not appear in
     * `$value` are absent from the map (no zero-count entries). The empty
     * string returns `[]`.
     *
     * @param string $value The string to inspect.
     * @param Encoding $encoding The character encoding of `$value`.
     *                                      Defaults to {@see Encoding::Utf8}.
     * @return array<string, int>           Map of character => occurrence count
     *                                      (every value is `>= 1`).
     *
     * @example
     *   Str::charStats('aabc');  // => ['a' => 2, 'b' => 1, 'c' => 1]
     *   Str::charStats('élève'); // => ['é' => 1, 'l' => 1, 'è' => 1, 'v' => 1, 'e' => 1]
     *   Str::charStats('');      // => []
     *
     * @see Encoding
     * @see https://www.php.net/manual/en/function.count-chars.php PHP native equivalent (byte-based)
     */
    public static function charStats(string $value, Encoding $encoding = Encoding::Utf8): array
    {
        $stats = [];
        $length = mb_strlen($value, $encoding->value);

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($value, $i, 1, $encoding->value);
            $stats[$char] = ($stats[$char] ?? 0) + 1;
        }

        return $stats;
    }
}
