<?php

declare(strict_types=1);

namespace Phyx\Arr;

use Phyx\Enums\Comparison;

/** Set-operation helpers for \Phyx\Arr. */
trait HandleSet
{
    /**
     * Remove duplicate values from the array.
     *
     * Iterates through the array and keeps only the first occurrence of each
     * value. Comparison can be strict or loose.
     *
     * @param array<array-key, mixed> $array      The source array.
     * @param Comparison              $comparison The comparison mode to use. {@see Comparison}. Defaults to Comparison::Strict.
     * @return array<array-key, mixed> A new array with unique values.
     *
     * @example Arr::unique([1, 1, 2, 2, 3]) // => [1, 2, 3]
     * @example Arr::unique(['1', 1], Comparison::Loose) // => ['1']
     *
     * @see array_unique
     */
    public static function unique(array $array, Comparison $comparison = Comparison::Strict): array
    {
        $seen = [];
        $result = [];
        foreach ($array as $key => $value) {
            if (!self::arrContainsValue($seen, $value, $comparison)) {
                $seen[] = $value;
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Remove duplicate values selected by a callback, key, or path.
     *
     * Iterates through the array and keeps the first item for each selected
     * value. String and integer selectors read direct keys or dot-separated
     * paths from arrays and public object properties. Missing selectors resolve
     * to null, so only the first item with a missing selector is preserved.
     * Keys from the original array are preserved.
     *
     * @param array<array-key, mixed>                       $array    The source array.
     * @param (callable(mixed, array-key): mixed)|int|string $selector The value selector used for uniqueness.
     * @return array<array-key, mixed> A new array with unique selected values.
     *
     * @example Arr::uniqueBy([['email' => 'a'], ['email' => 'a']], 'email') // => [['email' => 'a']]
     * @example Arr::uniqueBy($users, fn($user) => $user->email) // => unique users by email
     *
     * @see {@see Arr::unique}
     */
    public static function uniqueBy(array $array, mixed $selector): array
    {
        $seen = [];
        $result = [];
        foreach ($array as $key => $value) {
            $exists = false;
            $selected = self::resolveSelector($value, $key, $selector, $exists);
            if (!$exists) {
                $selected = null;
            }
            if (!self::containsStrictValue($seen, $selected)) {
                $seen[] = $selected;
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Retrieve duplicate values from the array.
     *
     * Returns an array containing all values that appear more than once in
     * the original array.
     *
     * @param array<array-key, mixed> $array      The source array.
     * @param Comparison              $comparison The comparison mode to use. {@see Comparison}. Defaults to Comparison::Strict.
     * @return array<array-key, mixed> An array containing the duplicates.
     *
     * @example Arr::duplicates([1, 1, 2, 3, 3]) // => [1 => 1, 4 => 3]
     *
     * @see {@see Arr::unique}
     */
    public static function duplicates(array $array, Comparison $comparison = Comparison::Strict): array
    {
        $seen = [];
        $duplicates = [];
        foreach ($array as $key => $value) {
            if (self::arrContainsValue($seen, $value, $comparison)) {
                $duplicates[$key] = $value;
            } else {
                $seen[] = $value;
            }
        }

        return $duplicates;
    }

    /**
     * Compute the difference between multiple arrays.
     *
     * Compares the first array against one or more other arrays and returns
     * the values in the first array that are not present in any of the others.
     *
     * @param array<array-key, mixed> $array     The source array.
     * @param array<array-key, mixed> ...$others One or more arrays to compare against.
     * @return array<array-key, mixed> Values in the first array not present in others.
     *
     * @example Arr::diff([1, 2, 3], [2, 4]) // => [0 => 1, 2 => 3]
     *
     * @see array_diff
     */
    public static function diff(array $array, array ...$others): array
    {
        return array_diff($array, ...$others);
    }

    /**
     * Compute the intersection of multiple arrays.
     *
     * Returns an array containing all the values of the first array that are
     * present in all the other arrays.
     *
     * @param array<array-key, mixed> $array     The source array.
     * @param array<array-key, mixed> ...$others One or more arrays to compare against.
     * @return array<array-key, mixed> Values present in all arrays.
     *
     * @example Arr::intersect([1, 2, 3], [2, 3, 4]) // => [1 => 2, 2 => 3]
     *
     * @see array_intersect
     */
    public static function intersect(array $array, array ...$others): array
    {
        return array_intersect($array, ...$others);
    }

    /**
     * Compute the union of multiple arrays.
     *
     * Combines multiple arrays by taking the values from the first array and
     * appending values from subsequent arrays if their keys do not already exist.
     *
     * @param array<array-key, mixed> $array     The source array.
     * @param array<array-key, mixed> ...$others One or more arrays to merge.
     * @return array<array-key, mixed> The union of the arrays.
     *
     * @example Arr::union(['a' => 1], ['a' => 2, 'b' => 3]) // => ['a' => 1, 'b' => 3]
     *
     * @see array_replace
     */
    public static function union(array $array, array ...$others): array
    {
        foreach ($others as $other) {
            $array += $other;
        }

        return $array;
    }

    /** @param list<mixed> $values */
    private static function arrContainsValue(array $values, mixed $needle, Comparison $comparison): bool
    {
        foreach ($values as $value) {
            if ($comparison === Comparison::Strict ? $value === $needle : $value == $needle) {
                return true;
            }
        }

        return false;
    }
}
