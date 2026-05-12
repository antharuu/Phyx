<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Merge helpers for \Phyx\Arr. */
trait HandleMerge
{
    /**
     * Merge one or more arrays.
     *
     * Combines the elements of one or more arrays together so that the values of
     * one are appended to the end of the previous ones. It returns the resulting
     * array without mutating the inputs.
     *
     * @param array<array-key, mixed> $array     The initial array to merge.
     * @param array<array-key, mixed> ...$others Additional arrays to merge.
     * @return array<array-key, mixed> The merged array.
     *
     * @example Arr::merge(['a' => 1], ['b' => 2]) // => ['a' => 1, 'b' => 2]
     *
     * @see array_merge
     */
    public static function merge(array $array, array ...$others): array
    {
        return array_merge($array, ...$others);
    }

    /**
     * Recursively merge one or more arrays.
     *
     * Merges the elements of one or more arrays together recursively. If the
     * input arrays have the same string keys, then the values for these keys
     * are merged together into an array.
     *
     * @param array<array-key, mixed> $array     The initial array to merge.
     * @param array<array-key, mixed> ...$others Additional arrays to merge.
     * @return array<array-key, mixed> The merged array.
     *
     * @example Arr::mergeRecursive(['a' => ['b' => 1]], ['a' => ['c' => 2]]) // => ['a' => ['b' => 1, 'c' => 2]]
     *
     * @see array_merge_recursive
     */
    public static function mergeRecursive(array $array, array ...$others): array
    {
        return array_merge_recursive($array, ...$others);
    }

    /**
     * Replace elements from passed arrays into the first array.
     *
     * Replaces the values of the first array with the same keys from all the
     * following arrays. If a key from the first array exists in the second
     * array, its value will be replaced.
     *
     * @param array<array-key, mixed> $array     The array in which elements are replaced.
     * @param array<array-key, mixed> ...$others Arrays from which elements will be extracted.
     * @return array<array-key, mixed> The array with replaced values.
     *
     * @example Arr::replace(['a' => 1, 'b' => 2], ['b' => 3]) // => ['a' => 1, 'b' => 3]
     *
     * @see array_replace
     */
    public static function replace(array $array, array ...$others): array
    {
        return array_replace($array, ...$others);
    }

    /**
     * Recursively replace elements from passed arrays into the first array.
     *
     * Operates like replace(), but traverses into arrays and applies the
     * replacement logic to nested elements.
     *
     * @param array<array-key, mixed> $array     The array in which elements are replaced.
     * @param array<array-key, mixed> ...$others Arrays from which elements will be extracted.
     * @return array<array-key, mixed> The array with replaced values.
     *
     * @example Arr::replaceRecursive(['a' => ['b' => 1]], ['a' => ['b' => 2]]) // => ['a' => ['b' => 2]]
     *
     * @see array_replace_recursive
     */
    public static function replaceRecursive(array $array, array ...$others): array
    {
        return array_replace_recursive($array, ...$others);
    }

    /**
     * Return a copy with a value appended to the end.
     *
     * Adds the specified value as the last element of the array. A new array
     * is returned and the original is not mutated.
     *
     * @param array<array-key, mixed> $array The source array.
     * @param mixed                   $value The value to append.
     * @return array<array-key, mixed> A copy of the array with the value appended.
     *
     * @example Arr::append([1, 2], 3) // => [1, 2, 3]
     *
     * @see {@see Arr::prepend}
     */
    public static function append(array $array, mixed $value): array
    {
        $array[] = $value;
        return $array;
    }

    /**
     * Return a copy with a value prepended to the beginning.
     *
     * Adds the specified value as the first element of the array. If a key is
     * provided, the value is added with that key. Otherwise, the value is
     * prepended and numeric keys are reindexed.
     *
     * @param array<array-key, mixed> $array The source array.
     * @param mixed                   $value The value to prepend.
     * @param int|string|null         $key   Optional key for the prepended value. Defaults to null.
     * @return array<array-key, mixed> A copy of the array with the value prepended.
     *
     * @example Arr::prepend([1, 2], 0) // => [0, 1, 2]
     * @example Arr::prepend(['a' => 1], 2, 'b') // => ['b' => 2, 'a' => 1]
     *
     * @see array_unshift
     */
    public static function prepend(array $array, mixed $value, int|string|null $key = null): array
    {
        if ($key === null) {
            array_unshift($array, $value);
            return $array;
        }

        return [$key => $value] + $array;
    }
}
