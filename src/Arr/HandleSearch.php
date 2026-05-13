<?php

declare(strict_types=1);

namespace Phyx\Arr;

use Phyx\Enums\Comparison;

/** Search helpers for \Phyx\Arr. */
trait HandleSearch
{
    /**
     * Determine whether the array contains a given value.
     *
     * Searches the array for the specified value using the provided comparison
     * mode.
     *
     * @param array<array-key, mixed> $array      The array to search.
     * @param mixed                   $value      The value to look for.
     * @param Comparison              $comparison The comparison mode to use. {@see Comparison}. Defaults to Comparison::Strict.
     * @return bool True if the value is found, false otherwise.
     *
     * @example Arr::contains([1, 2, 3], 2) // => true
     * @example Arr::contains(['1', '2'], 1, Comparison::Loose) // => true
     *
     * @see in_array
     */
    public static function contains(array $array, mixed $value, Comparison $comparison = Comparison::Strict): bool
    {
        return in_array($value, $array, $comparison === Comparison::Strict);
    }

    /**
     * Return the key of the first occurrence of a value.
     *
     * Searches the array for the given value and returns its corresponding key
     * if found. Returns null if the value is not present.
     *
     * @param array<array-key, mixed> $array      The array to search.
     * @param mixed                   $value      The value to look for.
     * @param Comparison              $comparison The comparison mode to use. {@see Comparison}. Defaults to Comparison::Strict.
     * @return int|string|null The key of the value or null if not found.
     *
     * @example Arr::indexOf(['a' => 1, 'b' => 2], 2) // => 'b'
     *
     * @see array_search
     */
    public static function indexOf(array $array, mixed $value, Comparison $comparison = Comparison::Strict): int|string|null
    {
        $key = array_search($value, $array, $comparison === Comparison::Strict);
        return $key === false ? null : $key;
    }

    /**
     * Return the first element passing a given truth test.
     *
     * Iterates through the array and returns the first element for which the
     * callback returns true. If no callback is provided, the first element of
     * the array is returned. Returns the default value if no match is found.
     *
     * @param array<array-key, mixed>               $array    The source array.
     * @param callable(mixed, array-key): bool|null $callback The predicate to apply. Defaults to null.
     * @param mixed                                 $default  The value to return if no match is found. Defaults to null.
     * @return mixed The first passing element or the default value.
     *
     * @example Arr::first([1, 2, 3], fn($v) => $v > 1) // => 2
     * @example Arr::first([1, 2, 3]) // => 1
     *
     * @see {@see Arr::last}
     */
    public static function first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        foreach ($array as $key => $value) {
            if ($callback === null || $callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return the last element passing a given truth test.
     *
     * Iterates through the array and returns the last element for which the
     * callback returns true. If no callback is provided, the last element of
     * the array is returned. Returns the default value if no match is found.
     *
     * @param array<array-key, mixed>               $array    The source array.
     * @param callable(mixed, array-key): bool|null $callback The predicate to apply. Defaults to null.
     * @param mixed                                 $default  The value to return if no match is found. Defaults to null.
     * @return mixed The last passing element or the default value.
     *
     * @example Arr::last([1, 2, 3, 2], fn($v) => $v === 2) // => 2
     * @example Arr::last([1, 2, 3]) // => 3
     *
     * @see {@see Arr::first}
     */
    public static function last(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        $found = false;
        $result = $default;
        foreach ($array as $key => $value) {
            if ($callback === null || $callback($value, $key)) {
                $found = true;
                $result = $value;
            }
        }

        return $found ? $result : $default;
    }

    /**
     * Return the first element passing a given truth test.
     *
     * Iterates through the array and returns the first element for which the
     * callback returns true. This mirrors the intent of PHP 8.4's `array_find`
     * while remaining available on PHP 8.1. Returns the default value when no
     * element matches.
     *
     * @param array<array-key, mixed>          $array    The source array.
     * @param callable(mixed, array-key): bool $callback The predicate to apply.
     * @param mixed                            $default  The value to return if no match is found. Defaults to null.
     * @return mixed The first passing element or the default value.
     *
     * @example Arr::find([1, 2, 3], fn($v) => $v > 1) // => 2
     * @example Arr::find([1, 2, 3], fn($v) => $v > 9, 'none') // => 'none'
     *
     * @see {@see Arr::first}
     */
    public static function find(array $array, callable $callback, mixed $default = null): mixed
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return the key of the first element passing a given truth test.
     *
     * Iterates through the array and returns the first key for which the
     * callback returns true. This mirrors the intent of PHP 8.4's
     * `array_find_key` while preserving Phyx's value-then-key callback order.
     * Returns null when no element matches.
     *
     * @param array<array-key, mixed>          $array    The source array.
     * @param callable(mixed, array-key): bool $callback The predicate to apply.
     * @return int|string|null The first matching key or null if no match is found.
     *
     * @example Arr::findKey(['a' => 1, 'b' => 2], fn($v) => $v === 2) // => 'b'
     *
     * @see {@see Arr::find}
     */
    public static function findKey(array $array, callable $callback): int|string|null
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Determine whether at least one element passes a given truth test.
     *
     * Iterates through the array and stops as soon as the callback returns true.
     * Empty arrays return false. This mirrors the intent of PHP 8.4's
     * `array_any` while remaining available on PHP 8.1.
     *
     * @param array<array-key, mixed>          $array    The source array.
     * @param callable(mixed, array-key): bool $callback The predicate to apply.
     * @return bool True if any element passes, false otherwise.
     *
     * @example Arr::any([1, 2, 3], fn($v) => $v > 2) // => true
     * @example Arr::any([], fn($v) => true) // => false
     */
    public static function any(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether every element passes a given truth test.
     *
     * Iterates through the array and stops as soon as the callback returns
     * false. Empty arrays return true, matching universal quantification and
     * the behavior expected from PHP 8.4's `array_all` pattern.
     *
     * @param array<array-key, mixed>          $array    The source array.
     * @param callable(mixed, array-key): bool $callback The predicate to apply.
     * @return bool True if every element passes, false otherwise.
     *
     * @example Arr::all([2, 4, 6], fn($v) => $v % 2 === 0) // => true
     * @example Arr::all([], fn($v) => false) // => true
     */
    public static function all(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Filter the array using a given truth test.
     *
     * Returns a new array containing only the elements for which the callback
     * returns true.
     *
     * @param array<array-key, mixed>          $array        The array to filter.
     * @param callable(mixed, array-key): bool $callback     The predicate to apply.
     * @param bool                             $preserveKeys Whether to maintain the original keys. Defaults to true.
     * @return array<array-key, mixed> The filtered array.
     *
     * @example Arr::where([1, 2, 3, 4], fn($v) => $v % 2 === 0) // => [1 => 2, 3 => 4]
     *
     * @see array_filter
     */
    public static function where(array $array, callable $callback, bool $preserveKeys = true): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                if ($preserveKeys) {
                    $result[$key] = $value;
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Determine whether the array contains a given key.
     *
     * Checks if the specified key is present in the array.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @param int|string              $key   The key to look for.
     * @return bool True if the key exists, false otherwise.
     *
     * @example Arr::containsKey(['a' => 1], 'a') // => true
     *
     * @see array_key_exists
     */
    public static function containsKey(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array);
    }
}
