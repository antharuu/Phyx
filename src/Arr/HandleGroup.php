<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Grouping helpers for \Phyx\Arr. */
trait HandleGroup
{
    /**
     * Retrieve all values for a given selector from a list of items.
     *
     * Iterates through the source array and extracts each selected value. String
     * and integer selectors read direct keys or dot-separated paths from arrays
     * and public object properties. Callable selectors receive the item first,
     * then its key. Items with a missing value selector are skipped. When a key
     * selector is provided, items missing that key selector are also skipped.
     *
     * @param array<array-key, mixed>                           $array    The source list of items.
     * @param (callable(mixed, array-key): mixed)|int|string    $valueKey The selector used to extract values.
     * @param (callable(mixed, array-key): mixed)|int|string|null $keyKey The selector used for result keys. Defaults to null.
     * @return array<array-key, mixed> The list of plucked values.
     *
     * @example Arr::pluck([['id' => 1], ['id' => 2]], 'id') // => [1, 2]
     * @example Arr::pluck([['user' => ['email' => 'a@b.test']]], 'user.email') // => ['a@b.test']
     *
     * @see array_column
     */
    public static function pluck(array $array, mixed $valueKey, mixed $keyKey = null): array
    {
        $result = [];
        foreach ($array as $key => $item) {
            $valueExists = false;
            $value = self::resolveSelector($item, $key, $valueKey, $valueExists);
            if (!$valueExists) {
                continue;
            }

            if ($keyKey !== null) {
                $resultKeyExists = false;
                $resultKey = self::resolveSelector($item, $key, $keyKey, $resultKeyExists);
                if (!$resultKeyExists) {
                    continue;
                }
                $result[$resultKey] = $value;
            } else {
                $result[] = $value;
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
     * @param (callable(mixed, array-key): (array-key|list<array-key>))|int|string $callback The selector used to determine the group key(s).
     * @return array<array-key, list<mixed>> The grouped array.
     *
     * @example Arr::groupBy([1, 2, 3, 4], fn($v) => $v % 2 === 0 ? 'even' : 'odd') // => ['odd' => [1, 3], 'even' => [2, 4]]
     *
     * @see {@see Arr::keyBy}
     */
    public static function groupBy(array $array, mixed $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $exists = false;
            $groups = self::resolveSelector($value, $key, $callback, $exists);
            if (!$exists) {
                continue;
            }
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
     * @param (callable(mixed, array-key): array-key)|int|string $callback The selector used to determine the new key.
     * @return array<array-key, mixed> The reindexed array.
     *
     * @example Arr::keyBy([['id' => 1], ['id' => 2]], fn($v) => $v['id']) // => [1 => ['id' => 1], 2 => ['id' => 2]]
     *
     * @see {@see Arr::groupBy}
     */
    public static function keyBy(array $array, mixed $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $exists = false;
            $resolvedKey = self::resolveSelector($value, $key, $callback, $exists);
            if (!$exists) {
                continue;
            }
            $result[$resolvedKey] = $value;
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
