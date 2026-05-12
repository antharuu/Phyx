<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Transformation helpers for \Phyx\Arr. */
trait HandleTransform
{
    /**
     * Apply a callback to each element of the array.
     *
     * Iterates through the array and replaces each value with the result of
     * the callback. Keys are preserved.
     *
     * @param array<array-key, mixed>           $array    The array to map.
     * @param callable(mixed, array-key): mixed $callback The callback to apply to each element.
     * @return array<array-key, mixed> The mapped array.
     *
     * @example Arr::map([1, 2, 3], fn($v) => $v * 2) // => [2, 4, 6]
     *
     * @see array_map
     */
    public static function map(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $callback($value, $key);
        }

        return $result;
    }

    /**
     * Apply a callback to each key of the array.
     *
     * Iterates through the array and replaces each key with the result of
     * the callback. Values are preserved.
     *
     * @param array<array-key, mixed>               $array    The array to map.
     * @param callable(mixed, array-key): array-key $callback The callback to derive new keys.
     * @return array<array-key, mixed> The array with mapped keys.
     *
     * @example Arr::mapKeys(['a' => 1], fn($v, $k) => strtoupper($k)) // => ['A' => 1]
     */
    public static function mapKeys(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$callback($value, $key)] = $value;
        }

        return $result;
    }

    /**
     * Map the array into new key/value pairs.
     *
     * Iterates through the array and merges the associative arrays returned
     * by the callback into a single result array.
     *
     * @param array<array-key, mixed>                             $array    The array to map.
     * @param callable(mixed, array-key): array<array-key, mixed> $callback The callback returning key/value pairs.
     * @return array<array-key, mixed> The mapped array.
     *
     * @example Arr::mapWithKeys([1, 2], fn($v) => [$v => $v * 10]) // => [1 => 10, 2 => 20]
     */
    public static function mapWithKeys(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            foreach ($callback($value, $key) as $newKey => $newValue) {
                $result[$newKey] = $newValue;
            }
        }

        return $result;
    }

    /**
     * Filter the array using a given truth test.
     *
     * Returns a new array containing only the elements for which the callback
     * returns true. If no callback is provided, all falsy values are removed.
     *
     * @param array<array-key, mixed>               $array    The array to filter.
     * @param callable(mixed, array-key): bool|null $callback The predicate to apply. Defaults to null.
     * @return array<array-key, mixed> The filtered array.
     *
     * @example Arr::filter([0, 1, false, 2, '']) // => [1 => 1, 3 => 2]
     * @example Arr::filter([1, 2, 3], fn($v) => $v > 1) // => [1 => 2, 2 => 3]
     *
     * @see array_filter
     * @see {@see Arr::reject}
     */
    public static function filter(array $array, ?callable $callback = null): array
    {
        if ($callback === null) {
            return array_filter($array);
        }

        $result = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Filter the array by removing items passing a truth test.
     *
     * Returns a new array containing only the elements for which the callback
     * returns false.
     *
     * @param array<array-key, mixed>          $array    The array to filter.
     * @param callable(mixed, array-key): bool $callback The predicate to apply.
     * @return array<array-key, mixed> The filtered array.
     *
     * @example Arr::reject([1, 2, 3], fn($v) => $v > 1) // => [0 => 1]
     *
     * @see {@see Arr::filter}
     */
    public static function reject(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Reduce the array to a single value.
     *
     * Iterates through the array and passes the result of the previous iteration
     * along with the current element to the callback.
     *
     * @param array<array-key, mixed>                  $array    The array to reduce.
     * @param callable(mixed, mixed, array-key): mixed $callback The callback to apply.
     * @param mixed                                    $initial  The initial value. Defaults to null.
     * @return mixed The reduced value.
     *
     * @example Arr::reduce([1, 2, 3], fn($carry, $v) => $carry + $v, 0) // => 6
     *
     * @see array_reduce
     */
    public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
    {
        $carry = $initial;
        foreach ($array as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }
}
