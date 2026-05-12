<?php

declare(strict_types=1);

namespace Phyx\Num;

use InvalidArgumentException;

/**
 * Power, root, logarithm and exponential helpers for {@see \Phyx\Num}.
 */
trait HandlePower
{
    /**
     * Raise a number to an exponent.
     *
     * Computes the value raised to the power of the exponent. Returns an integer
     * if the result is a whole number, otherwise returns a float.
     *
     * @param int|float $value    The base value.
     * @param int|float $exponent The exponent value.
     *
     * @return int|float The result of the exponentiation.
     *
     * @example Num::power(2, 3)   // => 8
     * @example Num::power(9, 0.5) // => 3
     *
     * @see pow
     */
    public static function power(int|float $value, int|float $exponent): int|float
    {
        $result = $value ** $exponent;

        return is_float($result) && floor($result) == $result ? (int) $result : $result;
    }

    /**
     * Return the square root of a number.
     *
     * Calculates the square root of the given value. Returns NAN for negative values.
     *
     * @param int|float $value The number to process.
     *
     * @return float The square root of the value.
     *
     * @example Num::sqrt(9) // => 3.0
     * @example Num::sqrt(2) // => 1.4142135623730951
     *
     * @see sqrt
     */
    public static function sqrt(int|float $value): float
    {
        return sqrt($value);
    }

    /**
     * Return the logarithm of a number for the given base.
     *
     * Computes the logarithm. Throws {@see InvalidArgumentException} if the value
     * is non-positive or if the base is non-positive or equal to one.
     *
     * @param int|float $value The value to calculate the logarithm for.
     * @param float     $base  The logarithm base. Defaults to M_E (natural log).
     *
     * @return float The calculated logarithm.
     *
     * @example Num::log(M_E)      // => 1.0
     * @example Num::log(100, 10) // => 2.0
     *
     * @see log
     */
    public static function log(int|float $value, float $base = M_E): float
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Logarithm value must be greater than zero.');
        }

        if ($base <= 0 || $base == 1.0) {
            throw new InvalidArgumentException('Logarithm base must be greater than zero and different from one.');
        }

        return log($value, $base);
    }

    /**
     * Return e raised to a power.
     *
     * Calculates the exponent of e (the base of natural logarithms).
     *
     * @param int|float $value The exponent to raise e to.
     *
     * @return float The result of e^value.
     *
     * @example Num::exp(1) // => 2.718281828459045
     * @example Num::exp(0) // => 1.0
     *
     * @see exp
     */
    public static function exp(int|float $value): float
    {
        return exp($value);
    }
}
