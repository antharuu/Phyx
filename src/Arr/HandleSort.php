<?php

declare(strict_types=1);

namespace Phyx\Arr;

use Phyx\Enums\SortDirection;
use Phyx\Enums\SortMode;

/** Sorting helpers for \Phyx\Arr. */
trait HandleSort
{
    /**
     * Sort the values of the array.
     *
     * Returns a new array with elements sorted according to the specified
     * direction and mode. Keys can be optionally preserved.
     *
     * @param array<array-key, mixed> $array        The array to sort.
     * @param SortDirection           $direction    The sorting direction. {@see SortDirection}. Defaults to SortDirection::Ascending.
     * @param SortMode                $mode         The sorting mode. {@see SortMode}. Defaults to SortMode::Regular.
     * @param bool                    $preserveKeys Whether to maintain original keys. Defaults to false.
     * @return array<array-key, mixed> The sorted array.
     *
     * @example Arr::sort([3, 1, 2]) // => [1, 2, 3]
     * @example Arr::sort([1, 2, 3], SortDirection::Descending) // => [3, 2, 1]
     *
     * @see sort
     * @see asort
     */
    public static function sort(array $array, SortDirection $direction = SortDirection::Ascending, SortMode $mode = SortMode::Regular, bool $preserveKeys = false): array
    {
        $flag = self::arrSortFlag($mode);
        if ($preserveKeys) {
            $direction === SortDirection::Ascending ? asort($array, $flag) : arsort($array, $flag);
        } else {
            $direction === SortDirection::Ascending ? sort($array, $flag) : rsort($array, $flag);
        }

        return $array;
    }

    /**
     * Sort the array by its keys.
     *
     * Returns a new array with keys sorted according to the specified
     * direction and mode.
     *
     * @param array<array-key, mixed> $array     The array to sort.
     * @param SortDirection           $direction The sorting direction. {@see SortDirection}. Defaults to SortDirection::Ascending.
     * @param SortMode                $mode      The sorting mode. {@see SortMode}. Defaults to SortMode::Regular.
     * @return array<array-key, mixed> The array sorted by keys.
     *
     * @example Arr::sortKeys(['b' => 1, 'a' => 2]) // => ['a' => 2, 'b' => 1]
     *
     * @see ksort
     * @see krsort
     */
    public static function sortKeys(array $array, SortDirection $direction = SortDirection::Ascending, SortMode $mode = SortMode::Regular): array
    {
        $flag = self::arrSortFlag($mode);
        $direction === SortDirection::Ascending ? ksort($array, $flag) : krsort($array, $flag);
        return $array;
    }

    /**
     * Sort the array using a callback to determine the criteria.
     *
     * Iterates through the array and applies the callback to each element to
     * derive a sorting value. The array is then sorted based on those values.
     *
     * @param array<array-key, mixed>           $array     The array to sort.
     * @param callable(mixed, array-key): mixed $callback  The callback to extract sorting criteria.
     * @param SortDirection                     $direction The sorting direction. {@see SortDirection}. Defaults to SortDirection::Ascending.
     * @return array<array-key, mixed> The sorted array.
     *
     * @example Arr::sortBy(['a' => 10, 'b' => 5], fn($v) => $v) // => ['b' => 5, 'a' => 10]
     *
     * @see uksort
     */
    public static function sortBy(array $array, callable $callback, SortDirection $direction = SortDirection::Ascending): array
    {
        $criteria = [];
        foreach ($array as $key => $value) {
            $criteria[$key] = $callback($value, $key);
        }

        uksort($array, static function (int|string $leftKey, int|string $rightKey) use ($criteria, $direction): int {
            $comparison = $criteria[$leftKey] <=> $criteria[$rightKey];
            return $direction === SortDirection::Ascending ? $comparison : -$comparison;
        });

        return $array;
    }

    /**
     * Reverse the order of elements in the array.
     *
     * Returns a new array with elements in the opposite order.
     *
     * @param array<array-key, mixed> $array        The source array.
     * @param bool                    $preserveKeys Whether to maintain original keys. Defaults to false.
     * @return array<array-key, mixed> The reversed array.
     *
     * @example Arr::reverse([1, 2, 3]) // => [3, 2, 1]
     *
     * @see array_reverse
     */
    public static function reverse(array $array, bool $preserveKeys = false): array
    {
        return array_reverse($array, $preserveKeys);
    }

    private static function arrSortFlag(SortMode $mode): int
    {
        return match ($mode) {
            SortMode::Regular => SORT_REGULAR,
            SortMode::Numeric => SORT_NUMERIC,
            SortMode::String => SORT_STRING,
            SortMode::Natural => SORT_NATURAL,
        };
    }
}
