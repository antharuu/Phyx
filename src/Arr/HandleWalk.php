<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Walking and path conversion helpers for \Phyx\Arr. */
trait HandleWalk
{
    /**
     * Flatten a multi-dimensional array into a single level using dot notation.
     *
     * Traverses the array and joins nested keys with the specified separator.
     *
     * @param array<array-key, mixed> $array     The array to flatten.
     * @param string                  $separator The character used to join keys. Defaults to '.'.
     * @return array<string, mixed> The flattened array with path keys.
     *
     * @throws \InvalidArgumentException If the separator is empty.
     *
     * @example Arr::dot(['user' => ['id' => 1]]) // => ['user.id' => 1]
     *
     * @see {@see Arr::undot}
     */
    public static function dot(array $array, string $separator = '.'): array
    {
        if ($separator === '') {
            throw new \InvalidArgumentException('Path separator must not be empty.');
        }

        return self::arrDot($array, '', $separator);
    }

    /**
     * Expand a flattened dot-notation array into a multi-dimensional array.
     *
     * Inverse of the dot method. Splits keys by the separator to recreate
     * the nested structure.
     *
     * @param array<string, mixed> $array     The flattened array.
     * @param string               $separator The character used to split keys. Defaults to '.'.
     * @return array<array-key, mixed> The expanded multi-dimensional array.
     *
     * @throws \InvalidArgumentException If the separator is empty.
     *
     * @example Arr::undot(['user.id' => 1]) // => ['user' => ['id' => 1]]
     *
     * @see {@see Arr::dot}
     */
    public static function undot(array $array, string $separator = '.'): array
    {
        if ($separator === '') {
            throw new \InvalidArgumentException('Path separator must not be empty.');
        }

        $result = [];
        foreach ($array as $path => $value) {
            if ($path === '') {
                continue;
            }
            $segments = explode($separator, (string) $path);
            $current =& $result;
            foreach ($segments as $segment) {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }
                $current =& $current[$segment];
            }
            $current = $value;
            unset($current);
        }

        return $result;
    }

    /**
     * Apply a callback to every element of the array.
     *
     * Iterates through the array and replaces each value with the result of
     * the callback. Similar to map but intended for side effects or direct
     * replacement.
     *
     * @param array<array-key, mixed>           $array    The array to walk.
     * @param callable(mixed, array-key): mixed $callback The callback to apply to each element.
     * @return array<array-key, mixed> The modified array.
     *
     * @example Arr::walk([1, 2], fn($v) => $v + 1) // => [2, 3]
     *
     * @see array_walk
     * @see {@see Arr::walkRecursive}
     */
    public static function walk(array $array, callable $callback): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = $callback($value, $key);
        }

        return $array;
    }

    /**
     * Apply a callback recursively to every non-array element.
     *
     * Traverses the array and applies the callback to every leaf node.
     *
     * @param array<array-key, mixed>           $array    The array to walk.
     * @param callable(mixed, array-key): mixed $callback The callback to apply to each leaf element.
     * @return array<array-key, mixed> The modified array.
     *
     * @example Arr::walkRecursive(['a' => [1]], fn($v) => $v + 1) // => ['a' => [2]]
     *
     * @see array_walk_recursive
     * @see {@see Arr::walk}
     */
    public static function walkRecursive(array $array, callable $callback): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = is_array($value) ? self::walkRecursive($value, $callback) : $callback($value, $key);
        }

        return $array;
    }

    /**
     * @param array<array-key, mixed> $array
     * @return array<string, mixed>
     */
    private static function arrDot(array $array, string $prefix, string $separator): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . $separator . (string) $key;
            if (is_array($value) && $value !== []) {
                $result += self::arrDot($value, $path, $separator);
            } else {
                $result[$path] = $value;
            }
        }

        return $result;
    }
}
