<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Grouping helpers for \Phyx\Arr. */
trait HandleGroup
{
    /**
     * Retrieve all values for a given key from a list of arrays.
     *
     * Iterates through the source array and extracts the value associated with
     * the specified value key. If a key key is provided, it will be used to
     * index the resulting array. Items that are not arrays or are missing the
     * value key are skipped.
     *
     * @param array<array-key, mixed> $array    The source list of arrays.
     * @param int|string              $valueKey The key to extract values from.
     * @param int|string|null         $keyKey   The key to use for the result's keys. Defaults to null.
     * @return array<array-key, mixed> The list of plucked values.
     *
     * @example Arr::pluck([['id' => 1], ['id' => 2]], 'id') // => [1, 2]
     * @example Arr::pluck([['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']], 'name', 'id') // => [1 => 'A', 2 => 'B']
     *
     * @see array_column
     */
    public static function pluck(array $array, int|string $valueKey, int|string|null $keyKey = null): array
    {
        $result = [];
        foreach ($array as $item) {
            if (!is_array($item) || !array_key_exists($valueKey, $item)) {
                continue;
            }

            if ($keyKey !== null && array_key_exists($keyKey, $item)) {
                $result[$item[$keyKey]] = $item[$valueKey];
            } else {
                $result[] = $item[$valueKey];
            }
        }

        return $result;
    }

    /**
     * Group an array's items by a given criteria.
     *
     * Iterates through the array and passes each item to the callback. The
     * return value of the callback is used as the group key. If the callback
     * returns an array of keys, the item is added to all specified groups.
     *
     * @param array<array-key, mixed>                                  $array    The array to group.
     * @param callable(mixed, array-key): (array-key|list<array-key>) $callback The callback used to determine the group key(s).
     * @return array<array-key, list<mixed>> The grouped array.
     *
     * @example Arr::groupBy([1, 2, 3, 4], fn($v) => $v % 2 === 0 ? 'even' : 'odd') // => ['odd' => [1, 3], 'even' => [2, 4]]
     *
     * @see {@see Arr::keyBy}
     */
    public static function groupBy(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $groups = $callback($value, $key);
            if (!is_array($groups)) {
                $groups = [$groups];
            }
            foreach ($groups as $group) {
                $result[$group][] = $value;
            }
        }

        return $result;
    }

    /**
     * Reindex an array by a given criteria.
     *
     * Iterates through the array and uses the return value of the callback as
     * the new key for each item. If multiple items result in the same key,
     * only the last one will be preserved.
     *
     * @param array<array-key, mixed>               $array    The array to reindex.
     * @param callable(mixed, array-key): array-key $callback The callback used to determine the new key.
     * @return array<array-key, mixed> The reindexed array.
     *
     * @example Arr::keyBy([['id' => 1], ['id' => 2]], fn($v) => $v['id']) // => [1 => ['id' => 1], 2 => ['id' => 2]]
     *
     * @see {@see Arr::groupBy}
     */
    public static function keyBy(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$callback($value, $key)] = $value;
        }

        return $result;
    }

    /**
     * Partition an array into two based on a predicate.
     *
     * Splits the array into two collections: one containing items that pass the
     * truth test and another for those that do not. Keys are preserved in both
     * arrays.
     *
     * @param array<array-key, mixed>          $array    The array to partition.
     * @param callable(mixed, array-key): bool $callback The predicate used to split the array.
     * @return array{0: array<array-key, mixed>, 1: array<array-key, mixed>} A tuple containing [truthy, falsy].
     *
     * @example Arr::partition([1, 2, 3, 4], fn($v) => $v > 2) // => [[2 => 3, 3 => 4], [0 => 1, 1 => 2]]
     *
     * @see {@see Arr::where}
     */
    public static function partition(array $array, callable $callback): array
    {
        $truthy = [];
        $falsy = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $truthy[$key] = $value;
            } else {
                $falsy[$key] = $value;
            }
        }

        return [$truthy, $falsy];
    }
}
