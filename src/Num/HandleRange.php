<?php

declare(strict_types=1);

namespace Phyx\Num;

use InvalidArgumentException;
use Phyx\Enums\Boundary;

/**
 * Range and boundary helpers for {@see \Phyx\Num}.
 */
trait HandleRange
{
    /**
     * Clamp a number into an inclusive range.
     *
     * Ensures the value stays within the [min, max] interval. If the value is
     * less than min, returns min; if greater than max, returns max.
     * Throws {@see InvalidArgumentException} if min is not less than max.
     *
     * @param int|float $value The number to clamp.
     * @param int|float $min   The lower boundary.
     * @param int|float $max   The upper boundary.
     *
     * @return int|float The clamped value.
     *
     * @example Num::clamp(5, 1, 10)  // => 5
     * @example Num::clamp(15, 1, 10) // => 10
     */
    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        self::assertValidRange($min, $max);

        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;
    }

    /**
     * Determine whether a number is inside a range.
     *
     * Checks if the value falls within the specified boundaries. The boundary
     * inclusion is controlled by the {@see Boundary} enum.
     * Throws {@see InvalidArgumentException} if min is not less than max.
     *
     * @param int|float $value    The number to check.
     * @param int|float $min      The lower boundary.
     * @param int|float $max      The upper boundary.
     * @param Boundary  $boundary The boundary inclusion mode {@see Boundary}. Defaults to {@see Boundary::Inclusive}.
     *
     * @return bool True if the value is within the range, false otherwise.
     *
     * @example Num::between(5, 1, 10)                    // => true
     * @example Num::between(1, 1, 10, Boundary::Exclusive) // => false
     */
    public static function between(
        int|float $value,
        int|float $min,
        int|float $max,
        Boundary $boundary = Boundary::Inclusive,
    ): bool {
        self::assertValidRange($min, $max);

        return $boundary === Boundary::Inclusive
            ? $value >= $min && $value <= $max
            : $value > $min && $value < $max;
    }

    /**
     * Determine whether a number is outside a range.
     *
     * Negates the result of {@see Num::between}.
     *
     * @param int|float $value    The number to check.
     * @param int|float $min      The lower boundary.
     * @param int|float $max      The upper boundary.
     * @param Boundary  $boundary The boundary inclusion mode {@see Boundary}. Defaults to {@see Boundary::Inclusive}.
     *
     * @return bool True if the value is outside the range, false otherwise.
     *
     * @example Num::outside(15, 1, 10) // => true
     * @example Num::outside(5, 1, 10)  // => false
     */
    public static function outside(
        int|float $value,
        int|float $min,
        int|float $max,
        Boundary $boundary = Boundary::Inclusive,
    ): bool {
        return !self::between($value, $min, $max, $boundary);
    }

    /**
     * Normalize a number in a range to the 0..1 interval.
     *
     * Linearly maps the value from the [min, max] range to [0, 1].
     * Throws {@see InvalidArgumentException} if min is not less than max.
     *
     * @param int|float $value The number to normalize.
     * @param int|float $min   The lower boundary.
     * @param int|float $max   The upper boundary.
     *
     * @return float The normalized value between 0.0 and 1.0.
     *
     * @example Num::normalize(5, 0, 10)    // => 0.5
     * @example Num::normalize(75, 50, 100) // => 0.5
     */
    public static function normalize(int|float $value, int|float $min, int|float $max): float
    {
        self::assertValidRange($min, $max);

        return ($value - $min) / ($max - $min);
    }

    /**
     * Assert that a range has a valid lower and upper boundary.
     *
     * Validates that min is strictly less than max.
     * Throws {@see InvalidArgumentException} if invalid.
     *
     * @param int|float $min The lower boundary.
     * @param int|float $max The upper boundary.
     *
     * @throws InvalidArgumentException
     */
    private static function assertValidRange(int|float $min, int|float $max): void
    {
        if ($min >= $max) {
            throw new InvalidArgumentException('The minimum boundary must be lower than the maximum boundary.');
        }
    }
}
