<?php

declare(strict_types=1);

namespace Phyx\Num;

/**
 * Numeric predicate helpers for {@see \Phyx\Num}.
 */
trait HandleCheck
{
    /**
     * Determine whether an integer is even.
     *
     * Checks if the number is divisible by 2 with no remainder.
     *
     * @param int $value The integer to check.
     *
     * @return bool True if the number is even, false otherwise.
     *
     * @example Num::isEven(2) // => true
     * @example Num::isEven(3) // => false
     */
    public static function isEven(int $value): bool
    {
        return $value % 2 === 0;
    }

    /**
     * Determine whether an integer is odd.
     *
     * Checks if the number is not divisible by 2 with no remainder.
     *
     * @param int $value The integer to check.
     *
     * @return bool True if the number is odd, false otherwise.
     *
     * @example Num::isOdd(3) // => true
     * @example Num::isOdd(2) // => false
     */
    public static function isOdd(int $value): bool
    {
        return $value % 2 !== 0;
    }

    /**
     * Determine whether a number is greater than zero.
     *
     * Checks if the value is strictly greater than 0.
     *
     * @param int|float $value The number to check.
     *
     * @return bool True if the number is positive, false otherwise.
     *
     * @example Num::isPositive(1) // => true
     * @example Num::isPositive(0) // => false
     */
    public static function isPositive(int|float $value): bool
    {
        return $value > 0;
    }

    /**
     * Determine whether a number is less than zero.
     *
     * Checks if the value is strictly less than 0.
     *
     * @param int|float $value The number to check.
     *
     * @return bool True if the number is negative, false otherwise.
     *
     * @example Num::isNegative(-1) // => true
     * @example Num::isNegative(0)  // => false
     */
    public static function isNegative(int|float $value): bool
    {
        return $value < 0;
    }

    /**
     * Determine whether a number is exactly zero.
     *
     * Checks if the value is equal to 0.
     *
     * @param int|float $value The number to check.
     *
     * @return bool True if the number is zero, false otherwise.
     *
     * @example Num::isZero(0)   // => true
     * @example Num::isZero(0.0) // => true
     * @example Num::isZero(1)   // => false
     */
    public static function isZero(int|float $value): bool
    {
        return $value == 0;
    }

    /**
     * Determine whether a float is finite.
     *
     * Checks if the value is a legal finite number on this platform.
     *
     * @param float $value The value to check.
     *
     * @return bool True if the value is finite, false otherwise.
     *
     * @example Num::isFinite(1.2) // => true
     * @example Num::isFinite(INF) // => false
     *
     * @see is_finite
     */
    public static function isFinite(float $value): bool
    {
        return is_finite($value);
    }

    /**
     * Determine whether a float is infinite.
     *
     * Checks if the value is either positive or negative infinity.
     *
     * @param float $value The value to check.
     *
     * @return bool True if the value is infinite, false otherwise.
     *
     * @example Num::isInfinite(INF) // => true
     * @example Num::isInfinite(1.2) // => false
     *
     * @see is_infinite
     */
    public static function isInfinite(float $value): bool
    {
        return is_infinite($value);
    }

    /**
     * Determine whether a float is 'Not a Number'.
     *
     * Checks if the value is the result of an undefined mathematical operation.
     *
     * @param float $value The value to check.
     *
     * @return bool True if the value is NaN, false otherwise.
     *
     * @example Num::isNan(acos(1.01)) // => true
     * @example Num::isNan(1.2)        // => false
     *
     * @see is_nan
     */
    public static function isNan(float $value): bool
    {
        return is_nan($value);
    }
}
