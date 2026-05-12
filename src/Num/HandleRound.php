<?php

declare(strict_types=1);

namespace Phyx\Num;

use Phyx\Enums\Rounding;

/**
 * Rounding helpers for {@see \Phyx\Num}.
 */
trait HandleRound
{
    /**
     * Round a number with the selected strategy.
     *
     * Rounds the given value to the specified precision using the provided
     * {@see Rounding} mode. Supports standard PHP rounding modes and
     * additional modes like TowardZero and AwayFromZero.
     *
     * @param int|float $value     The number to round.
     * @param int       $precision The number of decimal places. Defaults to 0.
     * @param Rounding  $mode      The rounding strategy {@see Rounding}. Defaults to {@see Rounding::HalfUp}.
     *
     * @return float The rounded value.
     *
     * @example Num::round(1.5, 0)                  // => 2.0
     * @example Num::round(1.55, 1, Rounding::HalfDown) // => 1.5
     *
     * @see round
     */
    public static function round(
        int|float $value,
        int $precision = 0,
        Rounding $mode = Rounding::HalfUp,
    ): float {
        if ($mode === Rounding::TowardZero || $mode === Rounding::AwayFromZero) {
            $factor = 10 ** $precision;
            $scaled = $value * $factor;
            $rounded = self::roundScaled($scaled, $mode);

            return (float) ($rounded / $factor);
        }

        return (float) \round($value, $precision, match ($mode) {
            Rounding::HalfUp => PHP_ROUND_HALF_UP,
            Rounding::HalfDown => PHP_ROUND_HALF_DOWN,
            Rounding::HalfEven => PHP_ROUND_HALF_EVEN,
            Rounding::HalfOdd => PHP_ROUND_HALF_ODD,
        });
    }

    /**
     * Round a number up to the next integer value.
     *
     * Returns the next highest integer value by rounding up the value if necessary.
     *
     * @param int|float $value The number to process.
     *
     * @return float The value rounded up.
     *
     * @example Num::ceil(4.3)  // => 5.0
     * @example Num::ceil(-4.3) // => -4.0
     *
     * @see ceil
     */
    public static function ceil(int|float $value): float
    {
        return (float) ceil($value);
    }

    /**
     * Round a number down to the previous integer value.
     *
     * Returns the next lowest integer value by rounding down the value if necessary.
     *
     * @param int|float $value The number to process.
     *
     * @return float The value rounded down.
     *
     * @example Num::floor(4.3)  // => 4.0
     * @example Num::floor(-4.3) // => -5.0
     *
     * @see floor
     */
    public static function floor(int|float $value): float
    {
        return (float) floor($value);
    }

    /**
     * Truncate a number toward zero.
     *
     * Discards the fractional part of the number, effectively rounding towards zero.
     *
     * @param int|float $value The number to truncate.
     *
     * @return int The truncated integer value.
     *
     * @example Num::truncate(4.3)  // => 4
     * @example Num::truncate(-4.3) // => -4
     */
    public static function truncate(int|float $value): int
    {
        return (int) $value;
    }

    /**
     * Round a scaled number based on a specific strategy.
     *
     * Internal helper for rounding values that have already been multiplied
     * by a precision factor.
     *
     * @param int|float $scaled The scaled number.
     * @param Rounding  $mode   The rounding strategy {@see Rounding}.
     *
     * @return float The rounded result.
     *
     * @example Num::roundScaled(1.5, Rounding::TowardZero) // => 1.0
     */
    private static function roundScaled(int|float $scaled, Rounding $mode): float
    {
        $isNegative = $scaled < 0;

        if ($mode === Rounding::TowardZero) {
            return $isNegative ? ceil($scaled) : floor($scaled);
        }

        return $isNegative ? floor($scaled) : ceil($scaled);
    }
}
