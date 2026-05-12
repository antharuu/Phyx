<?php

declare(strict_types=1);

namespace Phyx\Num;

use DivisionByZeroError;
use InvalidArgumentException;

/**
 * Base conversion and ratio helpers for {@see \Phyx\Num}.
 */
trait HandleConvert
{
    /**
     * Convert a string number from one base to another.
     *
     * Converts a string representation of a number in base $from to base $to.
     * Handles negative signs. Supported bases are 2 through 36.
     * Throws {@see InvalidArgumentException} for invalid bases or characters.
     *
     * @param string $value The numeric string to convert.
     * @param int    $from  The source base (2-36).
     * @param int    $to    The target base (2-36).
     *
     * @return string The converted numeric string.
     *
     * @example Num::convertBase('ff', 16, 10)   // => '255'
     * @example Num::convertBase('1010', 2, 10) // => '10'
     *
     * @see base_convert
     */
    public static function convertBase(string $value, int $from, int $to): string
    {
        self::assertBase($from);
        self::assertBase($to);
        self::assertDigitsForBase($value, $from);

        $negative = str_starts_with($value, '-');
        $digits = $negative ? substr($value, 1) : $value;
        $converted = base_convert($digits, $from, $to);

        return $negative && $converted !== '0' ? '-' . $converted : $converted;
    }

    /**
     * Convert a decimal integer to another base.
     *
     * Converts an integer to its string representation in the specified base.
     * Supported bases are 2 through 36.
     *
     * @param int $value The integer to convert.
     * @param int $base  The target base (2-36).
     *
     * @return string The converted numeric string.
     *
     * @example Num::toBase(255, 16) // => 'ff'
     * @example Num::toBase(10, 2)   // => '1010'
     */
    public static function toBase(int $value, int $base): string
    {
        self::assertBase($base);

        return self::convertBase((string) $value, 10, $base);
    }

    /**
     * Convert a string number from a specific base to decimal.
     *
     * Parses a numeric string in the given base and returns its decimal value.
     * Returns an int if within range, otherwise a float.
     *
     * @param string $value The numeric string to convert.
     * @param int    $base  The source base (2-36).
     *
     * @return int|float The decimal value.
     *
     * @example Num::fromBase('ff', 16)   // => 255
     * @example Num::fromBase('1010', 2) // => 10
     */
    public static function fromBase(string $value, int $base): int|float
    {
        $converted = self::convertBase($value, $base, 10);

        if (strlen(ltrim($converted, '-')) < strlen((string) PHP_INT_MAX)
            || (strlen(ltrim($converted, '-')) === strlen((string) PHP_INT_MAX) && ltrim($converted, '-') <= (string) PHP_INT_MAX)
        ) {
            return (int) $converted;
        }

        return (float) $converted;
    }

    /**
     * Calculate the ratio of a value over a total.
     *
     * Returns the result of dividing the value by the total.
     * Throws {@see DivisionByZeroError} if the total is zero.
     *
     * @param int|float $value The numerator.
     * @param int|float $total The denominator.
     *
     * @return float The calculated ratio.
     *
     * @example Num::ratio(1, 4) // => 0.25
     * @example Num::ratio(2, 1) // => 2.0
     */
    public static function ratio(int|float $value, int|float $total): float
    {
        if ($total == 0) {
            throw new DivisionByZeroError('Cannot calculate a ratio with a zero total.');
        }

        return (float) $value / (float) $total;
    }

    /**
     * Calculate the percentage of a value over a total.
     *
     * Calculates the ratio and multiplies by 100.
     * Throws {@see DivisionByZeroError} if the total is zero.
     *
     * @param int|float $value The numerator.
     * @param int|float $total The denominator.
     *
     * @return float The calculated percentage.
     *
     * @example Num::percentage(1, 4) // => 25.0
     * @example Num::percentage(1, 3) // => 33.333333333333336
     */
    public static function percentage(int|float $value, int|float $total): float
    {
        return self::ratio($value, $total) * 100;
    }

    /**
     * Assert that a base is within the valid range.
     *
     * Validates that the base is between 2 and 36 inclusive.
     * Throws {@see InvalidArgumentException} if invalid.
     *
     * @param int $base The base to validate.
     *
     * @throws InvalidArgumentException
     */
    private static function assertBase(int $base): void
    {
        if ($base < 2 || $base > 36) {
            throw new InvalidArgumentException('Base must be between 2 and 36.');
        }
    }

    /**
     * Assert that all digits in a string are valid for a given base.
     *
     * Checks each character of the string against the allowed alphabet for the specified base.
     * Throws {@see InvalidArgumentException} if invalid characters are found or if the string is empty.
     *
     * @param string $value The numeric string to validate.
     * @param int    $base  The base to check against.
     *
     * @throws InvalidArgumentException
     */
    private static function assertDigitsForBase(string $value, int $base): void
    {
        $digits = strtolower(ltrim($value, '-'));
        if ($digits === '') {
            throw new InvalidArgumentException('A base conversion value cannot be empty.');
        }

        $alphabet = substr('0123456789abcdefghijklmnopqrstuvwxyz', 0, $base);
        for ($index = 0, $length = strlen($digits); $index < $length; $index++) {
            if (strpos($alphabet, $digits[$index]) === false) {
                throw new InvalidArgumentException('The value contains digits that are invalid for the base.');
            }
        }
    }
}
