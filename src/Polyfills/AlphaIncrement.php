<?php

declare(strict_types=1);

namespace Phyx\Polyfills;

use ValueError;

/**
 * Polyfill for PHP 8.3's `str_increment()` / `str_decrement()`.
 *
 * Reproduces the semantics of the native functions so that Phyx can run
 * unchanged on PHP 8.0–8.2 while still using the native implementation on
 * 8.3+ when available (callers gate the choice through `function_exists()`).
 *
 * The algorithm mirrors the spec described in the PHP RFC:
 *  - Input must be a non-empty alphanumeric ASCII string (`[0-9A-Za-z]+`).
 *  - On increment, the rightmost character is bumped; carries propagate
 *    leftward (`z` → `a` + carry, `Z` → `A` + carry, `9` → `0` + carry).
 *    A carry past the leading character prepends a fresh character of the
 *    same class (`a`, `A` or `1`).
 *  - On decrement, the rightmost character is decreased; borrows propagate
 *    leftward (`a` → `z` + borrow, `A` → `Z` + borrow, `0` → `9` + borrow).
 *    A borrow past the leading character removes it; if no character is
 *    left, the function raises `ValueError`.
 *  - Inputs that would underflow (such as `'a'`, `'A'`, `'0'`, `'00'`) or
 *    that start with `'0'` raise `ValueError` on decrement.
 *
 * @internal Not part of the public Phyx API.
 */
final class AlphaIncrement
{
    /**
     * Polyfill of PHP 8.3's `str_increment()`.
     *
     * @param string $value A non-empty alphanumeric ASCII string.
     * @return string        The lexicographic increment of `$value`.
     *
     * @throws ValueError When `$value` is empty or contains a non-alphanumeric ASCII character.
     */
    public static function increment(string $value): string
    {
        self::assertAlphaNumeric($value, __FUNCTION__);

        $chars = str_split($value);
        $i = count($chars) - 1;
        $carry = true;

        while ($carry && $i >= 0) {
            [$chars[$i], $carry] = self::bumpUp($chars[$i]);
            $i--;
        }

        if ($carry) {
            array_unshift($chars, self::leadingSeedFor($value[0]));
        }

        return implode('', $chars);
    }

    /**
     * Validate that `$value` is non-empty and matches `[0-9A-Za-z]+`.
     *
     * @throws ValueError When the input is empty or contains forbidden characters.
     */
    private static function assertAlphaNumeric(string $value, string $caller): void
    {
        if ($value === '') {
            throw new ValueError(
                $caller . '(): Argument #1 ($string) cannot be empty',
            );
        }

        if (!ctype_alnum($value)) {
            throw new ValueError(
                $caller . '(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters',
            );
        }
    }

    /**
     * Increment a single alphanumeric character.
     *
     * @return array{string, bool} The new character and whether a carry occurred.
     */
    private static function bumpUp(string $char): array
    {
        return match (true) {
            $char === 'z' => ['a', true],
            $char === 'Z' => ['A', true],
            $char === '9' => ['0', true],
            $char >= 'a' && $char <= 'y' => [chr(ord($char) + 1), false],
            $char >= 'A' && $char <= 'Y' => [chr(ord($char) + 1), false],
            $char >= '0' && $char <= '8' => [chr(ord($char) + 1), false],
            default => [$char, false], // Unreachable: vetted by assertAlphaNumeric.
        };
    }

    /**
     * The seed character to prepend when an increment carry escapes the leading position.
     */
    private static function leadingSeedFor(string $firstChar): string
    {
        return match (true) {
            $firstChar >= 'a' && $firstChar <= 'z' => 'a',
            $firstChar >= 'A' && $firstChar <= 'Z' => 'A',
            default => '1',
        };
    }

    /**
     * Polyfill of PHP 8.3's `str_decrement()`.
     *
     * @param string $value A non-empty alphanumeric ASCII string greater than the minimum
     *                       representable value of its class (`'a'`, `'A'` or `'0'`).
     * @return string        The lexicographic decrement of `$value`.
     *
     * @throws ValueError When `$value` is empty, non-alphanumeric, starts with `'0'`,
     *                     or would underflow.
     */
    public static function decrement(string $value): string
    {
        self::assertAlphaNumeric($value, __FUNCTION__);

        if ($value[0] === '0') {
            throw new ValueError(
                'str_decrement(): Argument #1 ($string) "' . $value . '" is out of decrement range',
            );
        }

        if (strlen($value) === 1 && self::isMinimum($value)) {
            throw new ValueError(
                'str_decrement(): Argument #1 ($string) "' . $value . '" is out of decrement range',
            );
        }

        $chars = str_split($value);
        $i = count($chars) - 1;
        $borrow = true;

        while ($borrow && $i >= 0) {
            [$chars[$i], $borrow] = self::bumpDown($chars[$i]);
            $i--;
        }

        if ($borrow) {
            // Leading char wrapped around; drop it. The pre-checks on
            // empty/leading-zero input guarantee the remainder is non-empty
            // and does not start with '0', so no extra validation is needed here.
            array_shift($chars);
        }

        return implode('', $chars);
    }

    /**
     * Whether a single character is the minimum of its alphanumeric class.
     */
    private static function isMinimum(string $char): bool
    {
        return $char === 'a' || $char === 'A' || $char === '0';
    }

    /**
     * Decrement a single alphanumeric character.
     *
     * @return array{string, bool} The new character and whether a borrow occurred.
     */
    private static function bumpDown(string $char): array
    {
        return match (true) {
            $char === 'a' => ['z', true],
            $char === 'A' => ['Z', true],
            $char === '0' => ['9', true],
            $char >= 'b' && $char <= 'z' => [chr(ord($char) - 1), false],
            $char >= 'B' && $char <= 'Z' => [chr(ord($char) - 1), false],
            $char >= '1' && $char <= '9' => [chr(ord($char) - 1), false],
            default => [$char, false], // Unreachable.
        };
    }
}
