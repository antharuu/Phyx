<?php

declare(strict_types=1);

namespace Phyx\Arr;

use ValueError;

/** Randomization helpers for \Phyx\Arr. */
trait HandleRandom
{
    /**
     * Pick a random value from the array.
     *
     * Selects a single random element from the provided array.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return mixed A random value from the array.
     *
     * @throws \ValueError If the array is empty.
     *
     * @example Arr::random([1, 2, 3]) // => 2
     *
     * @see array_rand
     */
    public static function random(array $array): mixed
    {
        if ($array === []) {
            throw new ValueError('Cannot pick a random value from an empty array.');
        }

        return $array[array_rand($array)];
    }

    /**
     * Pick a random sample of values from the array.
     *
     * Selects the specified number of random elements from the array. If the
     * count is 0, an empty array is returned.
     *
     * @param array<array-key, mixed> $array The source array.
     * @param int                     $count The number of elements to sample.
     * @return array<array-key, mixed> A random sample of the array.
     *
     * @throws \ValueError If the count is negative or greater than the array size.
     *
     * @example Arr::sample([1, 2, 3, 4], 2) // => [1 => 2, 3 => 4]
     *
     * @see {@see Arr::random}
     */
    public static function sample(array $array, int $count): array
    {
        if ($count < 0 || $count > count($array)) {
            throw new ValueError('Sample count must be between 0 and the array size.');
        }
        if ($count === 0) {
            return [];
        }

        $keys = array_rand($array, $count);
        $keys = is_array($keys) ? $keys : [$keys];
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }

        return $result;
    }

    /**
     * Shuffle the values of the array.
     *
     * Randomizes the order of the values in the array. Numeric keys are
     * reindexed, and string keys are lost. Returns a new array without
     * mutating the original.
     *
     * @param array<array-key, mixed> $array The source array.
     * @return list<mixed> The shuffled array.
     *
     * @example Arr::shuffle([1, 2, 3]) // => [3, 1, 2]
     *
     * @see shuffle
     */
    public static function shuffle(array $array): array
    {
        $values = array_values($array);
        shuffle($values);
        return $values;
    }
}
