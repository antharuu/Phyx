<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Shape helpers for \Phyx\Arr. */
trait HandleShape
{
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * Iterates through the array and pulls nested values into the top-level
     * array. A depth limit can be specified. Keys are not preserved.
     *
     * @param array<array-key, mixed> $array The array to flatten.
     * @param int                     $depth The maximum number of levels to flatten. Defaults to PHP_INT_MAX.
     * @return array<array-key, mixed> The flattened array.
     *
     * @example Arr::flatten([[1, 2], [3, 4]]) // => [1, 2, 3, 4]
     * @example Arr::flatten([1, [2, [3]]], 1) // => [1, 2, [3]]
     *
     * @see {@see Arr::collapse}
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        if ($depth <= 0) {
            return $array;
        }

        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach (self::flatten($value, $depth - 1) as $nested) {
                    $result[] = $nested;
                }
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * Merges all nested arrays into a single array. String keys are preserved
     * (later values overwrite earlier ones), while integer keys are reindexed.
     *
     * @param array<array-key, mixed> $array The array of arrays to collapse.
     * @return array<array-key, mixed> The collapsed array.
     *
     * @example Arr::collapse([[1, 2], [3, 4]]) // => [1, 2, 3, 4]
     * @example Arr::collapse([['a' => 1], ['b' => 2]]) // => ['a' => 1, 'b' => 2]
     *
     * @see {@see Arr::flatten}
     */
    public static function collapse(array $array): array
    {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if (is_int($nestedKey)) {
                        $result[] = $nestedValue;
                    } else {
                        $result[$nestedKey] = $nestedValue;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Split an array into chunks of a specified size.
     *
     * Divides the array into multiple arrays, each containing the given number
     * of elements. The last chunk may contain fewer elements.
     *
     * @param array<array-key, mixed> $array        The array to chunk.
     * @param int                     $size         The size of each chunk. Must be at least 1.
     * @param bool                    $preserveKeys Whether to maintain original keys. Defaults to false.
     * @return list<array<array-key, mixed>> A list of chunks.
     *
     * @throws \ValueError If the size is less than 1.
     *
     * @example Arr::chunk([1, 2, 3, 4], 2) // => [[1, 2], [3, 4]]
     *
     * @see array_chunk
     */
    public static function chunk(array $array, int $size, bool $preserveKeys = false): array
    {
        if ($size < 1) {
            throw new \ValueError('Chunk size must be greater than or equal to 1.');
        }

        return array_chunk($array, $size, $preserveKeys);
    }

    /**
     * Extract a slice of the array.
     *
     * Returns a sequence of elements from the array starting at the specified
     * offset and up to the given length.
     *
     * @param array<array-key, mixed> $array        The source array.
     * @param int                     $offset       The starting index. If negative, starts from the end.
     * @param int|null                $length       The number of elements to include. Defaults to null (all).
     * @param bool                    $preserveKeys Whether to maintain original keys. Defaults to true.
     * @return array<array-key, mixed> The extracted slice.
     *
     * @example Arr::slice([1, 2, 3, 4], 1, 2) // => [1 => 2, 2 => 3]
     *
     * @see array_slice
     */
    public static function slice(array $array, int $offset, ?int $length = null, bool $preserveKeys = true): array
    {
        return array_slice($array, $offset, $length, $preserveKeys);
    }

    /**
     * Take a specified number of items from the array.
     *
     * Returns a new array with the first N items. If the count is negative,
     * takes items from the end of the array.
     *
     * @param array<array-key, mixed> $array  The source array.
     * @param int                     $length The number of items to take.
     * @return array<array-key, mixed> The items taken.
     *
     * @example Arr::take([1, 2, 3], 2) // => [1, 2]
     * @example Arr::take([1, 2, 3], -1) // => [2 => 3]
     *
     * @see {@see Arr::slice}
     */
    public static function take(array $array, int $length): array
    {
        if ($length < 0) {
            return array_slice($array, $length, null, true);
        }

        return array_slice($array, 0, $length, true);
    }

    /**
     * Ensure the given value is an array.
     *
     * If the value is already an array, it is returned as-is. If the value is
     * null, an empty array is returned. Otherwise, it is wrapped in an array.
     *
     * @param mixed $value The value to wrap.
     * @return array<array-key, mixed> The wrapped array.
     *
     * @example Arr::wrap(1) // => [1]
     * @example Arr::wrap([1]) // => [1]
     * @example Arr::wrap(null) // => []
     */
    public static function wrap(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}
