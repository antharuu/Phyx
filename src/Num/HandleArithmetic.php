<?php

declare(strict_types=1);

namespace Phyx\Num;

use DivisionByZeroError;

/**
 * Arithmetic helpers for {@see \Phyx\Num}.
 */
trait HandleArithmetic
{
    /**
     * Return the absolute value of a number.
     *
     * Computes the magnitude of the number regardless of its sign.
     *
     * @param int|float $value The number to process.
     *
     * @return int|float The absolute value of the number.
     *
     * @example Num::abs(-4.2) // => 4.2
     * @example Num::abs(5)    // => 5
     *
     * @see abs
     */
    public static function abs(int|float $value): int|float
    {
        return abs($value);
    }

    /**
     * Return the floating-point remainder of a division.
     *
     * Computes the remainder of dividing the dividend by the divisor.
     * Throws {@see DivisionByZeroError} if the divisor is zero.
     *
     * @param int|float $value   The dividend.
     * @param int|float $divisor The divisor.
     *
     * @return float The floating-point remainder.
     *
     * @example Num::mod(5.7, 1.3)  // => 0.5
     * @example Num::mod(-5.7, 1.3) // => -0.5
     *
     * @see fmod
     */
    public static function mod(int|float $value, int|float $divisor): float
    {
        if ($divisor == 0) {
            throw new DivisionByZeroError('Modulo by zero is not allowed.');
        }

        return fmod((float) $value, (float) $divisor);
    }

    /**
     * Divide two integers and return the integer quotient.
     *
     * Performs an integer division. Throws {@see DivisionByZeroError} if the divisor is zero.
     *
     * @param int $value   The dividend.
     * @param int $divisor The divisor.
     *
     * @return int The integer quotient.
     *
     * @example Num::divideInt(10, 3)  // => 3
     * @example Num::divideInt(-10, 3) // => -3
     *
     * @see intdiv
     */
    public static function divideInt(int $value, int $divisor): int
    {
        if ($divisor === 0) {
            throw new DivisionByZeroError('Integer division by zero is not allowed.');
        }

        return intdiv($value, $divisor);
    }

    /**
     * Return the sign of a number.
     *
     * Returns -1 if the number is negative, 0 if it is zero, or 1 if it is positive.
     *
     * @param int|float $value The number to evaluate.
     *
     * @return int The sign indicator (-1, 0, or 1).
     *
     * @example Num::sign(-42) // => -1
     * @example Num::sign(0)   // => 0
     * @example Num::sign(42)  // => 1
     */
    public static function sign(int|float $value): int
    {
        return $value <=> 0;
    }

    /**
     * Calculate the percentage represented by a value over a total.
     *
     * Calculates (value / total) * 100. Throws {@see DivisionByZeroError} if the total is zero.
     *
     * @param int|float $value The numerator value.
     * @param int|float $total The denominator (total) value.
     *
     * @return float The calculated percentage.
     *
     * @example Num::percentageOf(50, 200) // => 25.0
     * @example Num::percentageOf(1, 3)     // => 33.333333333333336
     */
    public static function percentageOf(int|float $value, int|float $total): float
    {
        if ($total == 0) {
            throw new DivisionByZeroError('Cannot calculate a percentage of zero.');
        }

        return ((float) $value / (float) $total) * 100;
    }
}
