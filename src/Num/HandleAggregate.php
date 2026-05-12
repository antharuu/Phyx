<?php

declare(strict_types=1);

namespace Phyx\Num;

/**
 * Aggregate helpers for {@see \Phyx\Num}.
 */
trait HandleAggregate
{
    /**
     * Return the smallest number from a list.
     *
     * Iterates through the provided list to find the minimum value. Returns null if the list is empty.
     *
     * @param list<int|float> $numbers The list of numbers to evaluate.
     *
     * @return int|float|null The minimum value, or null if empty.
     *
     * @example Num::min([1, 2, 3]) // => 1
     * @example Num::min([]) // => null
     *
     * @see min
     */
    public static function min(array $numbers): int|float|null
    {
        return $numbers === [] ? null : min($numbers);
    }

    /**
     * Return the largest number from a list.
     *
     * Iterates through the provided list to find the maximum value. Returns null if the list is empty.
     *
     * @param list<int|float> $numbers The list of numbers to evaluate.
     *
     * @return int|float|null The maximum value, or null if empty.
     *
     * @example Num::max([1, 2, 3]) // => 3
     * @example Num::max([]) // => null
     *
     * @see max
     */
    public static function max(array $numbers): int|float|null
    {
        return $numbers === [] ? null : max($numbers);
    }

    /**
     * Calculate the sum of all numbers in a list.
     *
     * Sums all elements in the provided list. Returns 0 if the list is empty.
     *
     * @param list<int|float> $numbers The list of numbers to sum.
     *
     * @return int|float The total sum.
     *
     * @example Num::sum([1, 2, 3]) // => 6
     * @example Num::sum([]) // => 0
     *
     * @see array_sum
     */
    public static function sum(array $numbers): int|float
    {
        return array_sum($numbers);
    }

    /**
     * Calculate the arithmetic mean of a list of numbers.
     *
     * Divides the sum of the list by its count. Returns null if the list is empty to avoid division by zero.
     *
     * @param list<int|float> $numbers The list of numbers to average.
     *
     * @return float|null The average value, or null if empty.
     *
     * @example Num::average([1, 2, 3]) // => 2.0
     * @example Num::average([]) // => null
     */
    public static function average(array $numbers): ?float
    {
        if ($numbers === []) {
            return null;
        }

        return array_sum($numbers) / count($numbers);
    }

    /**
     * Calculate the median value of a list of numbers.
     *
     * Sorts the list numerically and finds the middle value. For an even number of elements,
     * it returns the average of the two middle values. Returns null if the list is empty.
     *
     * @param list<int|float> $numbers The list of numbers to evaluate.
     *
     * @return float|null The median value, or null if empty.
     *
     * @example Num::median([1, 3, 2]) // => 2.0
     * @example Num::median([1, 2, 3, 4]) // => 2.5
     * @example Num::median([]) // => null
     */
    public static function median(array $numbers): ?float
    {
        if ($numbers === []) {
            return null;
        }

        sort($numbers, SORT_NUMERIC);
        $count = count($numbers);
        $middle = intdiv($count, 2);

        if ($count % 2 === 1) {
            return (float) $numbers[$middle];
        }

        return ((float) $numbers[$middle - 1] + (float) $numbers[$middle]) / 2;
    }
}
