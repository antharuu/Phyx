<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Combination helpers for \Phyx\Arr. */
trait HandleCombine
{
    /**
     * Create an array by using one array for keys and another for its values.
     *
     * Combines two lists into an associative array. Both arrays must have the
     * same number of elements.
     *
     * @param list<array-key> $keys   The list of keys to use.
     * @param list<mixed>     $values The list of values to use.
     * @return array<array-key, mixed> The combined associative array.
     *
     * @example Arr::combine(['a', 'b'], [1, 2]) // => ['a' => 1, 'b' => 2]
     *
     * @see array_combine
     */
    public static function combine(array $keys, array $values): array
    {
        return array_combine($keys, $values);
    }

    /**
     * Zip multiple arrays together.
     *
     * Interleaves the values of the provided arrays into a list of tuples. The
     * resulting list length is determined by the shortest input array.
     *
     * @param array<array-key, mixed> ...$arrays The arrays to zip.
     * @return list<list<mixed>> A list of zipped tuples.
     *
     * @example Arr::zip(['a', 'b'], [1, 2]) // => [['a', 1], ['b', 2]]
     * @example Arr::zip([1, 2, 3], ['a', 'b']) // => [[1, 'a'], [2, 'b']]
     */
    public static function zip(array ...$arrays): array
    {
        if ($arrays === []) {
            return [];
        }

        $lists = array_map('array_values', $arrays);
        $lengths = array_map('count', $lists);
        $length = min($lengths);
        $result = [];
        for ($i = 0; $i < $length; $i++) {
            $row = [];
            foreach ($lists as $list) {
                $row[] = $list[$i];
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Return the first key-value pair as a tuple.
     *
     * Retrieves the first key and its corresponding value from the array. If
     * the array is empty, both elements in the pair will be null.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return array{0: mixed, 1: mixed} A tuple containing [key, value].
     *
     * @example Arr::pair(['a' => 1, 'b' => 2]) // => ['a', 1]
     * @example Arr::pair([]) // => [null, null]
     *
     * @see array_key_first
     */
    public static function pair(array $array): array
    {
        $key = array_key_first($array);
        return [$key, $key === null ? null : $array[$key]];
    }

    /**
     * Transpose a 2D array (swap rows and columns).
     *
     * Converts a list of rows into a list of columns. The resulting number of
     * columns is determined by the shortest row.
     *
     * @param list<array<array-key, mixed>> $rows The 2D array to transpose.
     * @return list<list<mixed>> The transposed 2D array.
     *
     * @example Arr::transpose([[1, 2], [3, 4]]) // => [[1, 3], [2, 4]]
     * @example Arr::transpose([[1, 2, 3], [4, 5]]) // => [[1, 4], [2, 5]]
     */
    public static function transpose(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $lists = array_map('array_values', $rows);
        $lengths = array_map('count', $lists);
        $length = min($lengths);
        $result = [];
        for ($column = 0; $column < $length; $column++) {
            $newRow = [];
            foreach ($lists as $row) {
                $newRow[] = $row[$column];
            }
            $result[] = $newRow;
        }

        return $result;
    }
}
