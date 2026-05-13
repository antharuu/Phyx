<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Access helpers for \Phyx\Arr. */
trait HandleAccess
{
    /**
     * Return a direct value from the array.
     *
     * Retrieves the value associated with the specified key. If the key does not
     * exist, returns the provided default value.
     *
     * @param array<array-key, mixed> $array   The source array to access.
     * @param int|string              $key     The key to retrieve.
     * @param mixed                   $default The value to return if the key is missing. Defaults to null.
     * @return mixed The value found at the key or the default value.
     *
     * @example Arr::get(['a' => 1], 'a') // => 1
     * @example Arr::get(['a' => 1], 'b', 2) // => 2
     *
     * @see array_key_exists
     */
    public static function get(array $array, int|string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Return a copy containing only the requested direct keys.
     *
     * Iterates over the requested keys and copies the matching values from the
     * source array. Missing keys are ignored, original values are preserved, and
     * the result follows the order of the requested keys for predictable shaping.
     *
     * @param array<array-key, mixed> $array The source array to filter.
     * @param list<int|string>        $keys  The direct keys to keep.
     * @return array<array-key, mixed> A copy containing only the requested existing keys.
     *
     * @example Arr::only(['id' => 1, 'name' => 'Ada'], ['name']) // => ['name' => 'Ada']
     *
     * @see array_intersect_key
     */
    public static function only(array $array, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $result[$key] = $array[$key];
            }
        }

        return $result;
    }

    /**
     * Return a copy without the requested direct keys.
     *
     * Removes each requested key from a copy of the source array. Missing keys
     * are ignored and the remaining keys keep their original order and values.
     * This method never mutates the input array.
     *
     * @param array<array-key, mixed> $array The source array to filter.
     * @param list<int|string>        $keys  The direct keys to remove.
     * @return array<array-key, mixed> A copy without the requested keys.
     *
     * @example Arr::except(['id' => 1, 'password' => 'x'], ['password']) // => ['id' => 1]
     *
     * @see array_diff_key
     */
    public static function except(array $array, array $keys): array
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * Return a nested value addressed by a separated path.
     *
     * Traverses the array using the given path segments. If any segment is missing
     * or not an array during traversal, the default value is returned. An empty
     * path returns the default value.
     *
     * @param array<array-key, mixed> $array     The source array to traverse.
     * @param string                  $path      The dot-separated (or custom) path to the value.
     * @param mixed                   $default   The value to return if the path is missing. Defaults to null.
     * @param string                  $separator The character used to split the path into segments. Defaults to '.'.
     * @return mixed The nested value found at the path or the default value.
     *
     * @throws \InvalidArgumentException If the separator is an empty string.
     *
     * @example Arr::getPath(['user' => ['id' => 1]], 'user.id') // => 1
     * @example Arr::getPath(['user' => ['id' => 1]], 'user.name', 'Guest') // => 'Guest'
     *
     * @see {@see Arr::get}
     */
    public static function getPath(array $array, string $path, mixed $default = null, string $separator = '.'): mixed
    {
        if ($path === '') {
            return $default;
        }
        if ($separator === '') {
            throw new \InvalidArgumentException(self::PATH_SEPARATOR_EMPTY_MESSAGE);
        }

        $current = $array;
        foreach (explode($separator, $path) as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * Return a copy with a direct key set.
     *
     * Assigns the given value to the specified key in the array. This method
     * returns a new array and does not mutate the original.
     *
     * @param array<array-key, mixed> $array The source array to modify.
     * @param int|string              $key   The key to set.
     * @param mixed                   $value The value to assign.
     * @return array<array-key, mixed> A copy of the array with the key set.
     *
     * @example Arr::set(['a' => 1], 'b', 2) // => ['a' => 1, 'b' => 2]
     * @example Arr::set(['a' => 1], 'a', 3) // => ['a' => 3]
     */
    public static function set(array $array, int|string $key, mixed $value): array
    {
        $array[$key] = $value;
        return $array;
    }

    /**
     * Return a copy with a nested path set.
     *
     * Traverses the array using path segments and assigns the value. Intermediate
     * arrays are created if segments are missing or not arrays. An empty path
     * returns the original array.
     *
     * @param array<array-key, mixed> $array     The source array to modify.
     * @param string                  $path      The dot-separated path to set.
     * @param mixed                   $value     The value to assign.
     * @param string                  $separator The character used to split the path. Defaults to '.'.
     * @return array<array-key, mixed> A copy of the array with the path set.
     *
     * @throws \InvalidArgumentException If the separator is an empty string.
     *
     * @example Arr::setPath([], 'user.id', 1) // => ['user' => ['id' => 1]]
     * @example Arr::setPath(['a' => 1], 'a.b', 2) // => ['a' => ['b' => 2]]
     *
     * @see {@see Arr::set}
     */
    public static function setPath(array $array, string $path, mixed $value, string $separator = '.'): array
    {
        if ($path === '') {
            return $array;
        }
        if ($separator === '') {
            throw new \InvalidArgumentException(self::PATH_SEPARATOR_EMPTY_MESSAGE);
        }

        $segments = explode($separator, $path);
        $current =& $array;
        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current =& $current[$segment];
        }
        $current = $value;
        unset($current);

        return $array;
    }

    /**
     * Return a copy without a direct key.
     *
     * Removes the specified key from the array if it exists. Returns a new
     * array without mutating the original.
     *
     * @param array<array-key, mixed> $array The source array to modify.
     * @param int|string              $key   The key to remove.
     * @return array<array-key, mixed> A copy of the array without the key.
     *
     * @example Arr::forget(['a' => 1, 'b' => 2], 'a') // => ['b' => 2]
     *
     * @see unset
     */
    public static function forget(array $array, int|string $key): array
    {
        unset($array[$key]);
        return $array;
    }

    /**
     * Return a copy without a nested path.
     *
     * Traverses the array to the specified path and removes the final segment.
     * If the path does not exist, the original array is returned.
     *
     * @param array<array-key, mixed> $array     The source array to modify.
     * @param string                  $path      The dot-separated path to remove.
     * @param string                  $separator The character used to split the path. Defaults to '.'.
     * @return array<array-key, mixed> A copy of the array without the path.
     *
     * @throws \InvalidArgumentException If the separator is an empty string.
     *
     * @example Arr::forgetPath(['user' => ['id' => 1]], 'user.id') // => ['user' => []]
     *
     * @see {@see Arr::forget}
     */
    public static function forgetPath(array $array, string $path, string $separator = '.'): array
    {
        if ($path === '') {
            return $array;
        }
        if ($separator === '') {
            throw new \InvalidArgumentException(self::PATH_SEPARATOR_EMPTY_MESSAGE);
        }

        $segments = explode($separator, $path);
        $last = array_pop($segments);
        $current =& $array;
        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                return $array;
            }
            $current =& $current[$segment];
        }
        unset($current[$last]);
        unset($current);

        return $array;
    }

    /**
     * Determine whether a direct key exists.
     *
     * Checks if the specified key is present in the array, even if the value
     * is null.
     *
     * @param array<array-key, mixed> $array The array to check.
     * @param int|string              $key   The key to look for.
     * @return bool True if the key exists, false otherwise.
     *
     * @example Arr::hasKey(['a' => 1], 'a') // => true
     * @example Arr::hasKey(['a' => null], 'a') // => true
     * @example Arr::hasKey(['a' => 1], 'b') // => false
     *
     * @see array_key_exists
     */
    public static function hasKey(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Determine whether a nested path exists.
     *
     * Traverses the array to check for the existence of the specified path,
     * including null values. An empty path returns false.
     *
     * @param array<array-key, mixed> $array     The array to check.
     * @param string                  $path      The dot-separated path to check.
     * @param string                  $separator The character used to split the path. Defaults to '.'.
     * @return bool True if the path exists, false otherwise.
     *
     * @throws \InvalidArgumentException If the separator is an empty string.
     *
     * @example Arr::hasPath(['user' => ['id' => 1]], 'user.id') // => true
     * @example Arr::hasPath(['user' => ['id' => null]], 'user.id') // => true
     *
     * @see {@see Arr::hasKey}
     */
    public static function hasPath(array $array, string $path, string $separator = '.'): bool
    {
        if ($path === '') {
            return false;
        }
        if ($separator === '') {
            throw new \InvalidArgumentException(self::PATH_SEPARATOR_EMPTY_MESSAGE);
        }

        $current = $array;
        foreach (explode($separator, $path) as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return false;
            }
            $current = $current[$segment];
        }

        return true;
    }
}
