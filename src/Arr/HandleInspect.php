<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Inspection helpers for \Phyx\Arr. */
trait HandleInspect
{
    /**
     * Determine whether the array is a list.
     *
     * An array is considered a list if its keys consist of consecutive integers
     * from 0 to count($array) - 1.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @return bool True if the array is a list, false otherwise.
     *
     * @example Arr::isList([1, 2, 3]) // => true
     * @example Arr::isList(['a' => 1]) // => false
     *
     * @see array_is_list
     */
    public static function isList(array $array): bool
    {
        return array_is_list($array);
    }

    /**
     * Determine whether the array is associative.
     *
     * An array is considered associative if it is not a list. This includes
     * arrays with non-sequential or non-integer keys.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @return bool True if the array is associative, false otherwise.
     *
     * @example Arr::isAssoc(['a' => 1]) // => true
     * @example Arr::isAssoc([1, 2, 3]) // => false
     *
     * @see {@see Arr::isList}
     */
    public static function isAssoc(array $array): bool
    {
        return !array_is_list($array);
    }

    /**
     * Determine whether the array is empty.
     *
     * Checks if the array contains zero elements.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @return bool True if the array is empty, false otherwise.
     *
     * @example Arr::isEmpty([]) // => true
     * @example Arr::isEmpty([1]) // => false
     */
    public static function isEmpty(array $array): bool
    {
        return $array === [];
    }

    /**
     * Determine whether the array is not empty.
     *
     * Checks if the array contains one or more elements.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @return bool True if the array is not empty, false otherwise.
     *
     * @example Arr::isNotEmpty([1]) // => true
     * @example Arr::isNotEmpty([]) // => false
     *
     * @see {@see Arr::isEmpty}
     */
    public static function isNotEmpty(array $array): bool
    {
        return $array !== [];
    }

    /**
     * Return the number of elements in the array.
     *
     * Counts all elements in the array.
     *
     * @param array<array-key, mixed> $array The array to count.
     * @return int The number of elements.
     *
     * @example Arr::count([1, 2, 3]) // => 3
     *
     * @see count
     */
    public static function count(array $array): int
    {
        return count($array);
    }

    /**
     * Return all keys from the array.
     *
     * Extracts all keys from the provided array in their current order.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return list<array-key> A list of keys.
     *
     * @example Arr::keys(['a' => 1, 'b' => 2]) // => ['a', 'b']
     *
     * @see array_keys
     */
    public static function keys(array $array): array
    {
        return array_keys($array);
    }

    /**
     * Return all values from the array.
     *
     * Extracts all values from the provided array, reindexing them numerically.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return list<mixed> A list of values.
     *
     * @example Arr::values(['a' => 1, 'b' => 2]) // => [1, 2]
     *
     * @see array_values
     */
    public static function values(array $array): array
    {
        return array_values($array);
    }

    /**
     * Return the first key of the array.
     *
     * Retrieves the first key defined in the array without affecting the
     * internal array pointer. Returns null if the array is empty.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return int|string|null The first key or null if empty.
     *
     * @example Arr::firstKey(['a' => 1, 'b' => 2]) // => 'a'
     *
     * @see array_key_first
     */
    public static function firstKey(array $array): int|string|null
    {
        return array_key_first($array);
    }

    /**
     * Return the last key of the array.
     *
     * Retrieves the last key defined in the array without affecting the
     * internal array pointer. Returns null if the array is empty.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return int|string|null The last key or null if empty.
     *
     * @example Arr::lastKey(['a' => 1, 'b' => 2]) // => 'b'
     *
     * @see array_key_last
     */
    public static function lastKey(array $array): int|string|null
    {
        return array_key_last($array);
    }
}
