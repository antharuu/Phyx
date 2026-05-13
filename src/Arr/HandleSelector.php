<?php

declare(strict_types=1);

namespace Phyx\Arr;

/** Selector resolution helpers shared by array collection operations. */
trait HandleSelector
{
    /**
     * Resolve a selector against an item.
     *
     * Selectors are intentionally simple and predictable: closures/callables are
     * executed with Phyx's standard value-then-key argument order, while string
     * and integer selectors are treated as direct keys or dot-separated paths.
     * String callables such as `trim` are therefore considered paths; wrap them
     * in a closure when callable behavior is desired.
     *
     * The path resolver supports arrays and public object properties, including
     * mixed array/object paths. Missing segments leave `$exists` as false and
     * return null. Existing null values leave `$exists` as true.
     *
     * @param mixed                                                          $item     The item to read from.
     * @param int|string                                                     $key      The item's original key.
     * @param mixed      $selector The selector to resolve.
     * @param bool       $exists   Set to true when the selector resolves to an existing value.
     * @return mixed The selected value, or null when the selector is missing.
     *
     * @throws \TypeError If the selector is not a callable, string, or integer.
     */
    private static function resolveSelector(mixed $item, int|string $key, mixed $selector, bool &$exists): mixed
    {
        if (is_string($selector) || is_int($selector)) {
            return self::resolvePathSelector($item, (string) $selector, $exists);
        }

        if (!is_callable($selector)) {
            throw new \TypeError('Array selector must be a callable, string, or integer.');
        }

        $exists = true;
        return $selector($item, $key);
    }

    /**
     * Resolve a direct key or dot-separated path against an item.
     *
     * @param mixed  $item   The item to read from.
     * @param string $path   The direct key or dot-separated path to resolve.
     * @param bool   $exists Set to true when the path resolves to an existing value.
     * @return mixed The resolved value, or null when the path is missing.
     */
    private static function resolvePathSelector(mixed $item, string $path, bool &$exists): mixed
    {
        $exists = false;
        $current = $item;
        foreach (explode('.', $path) as $segment) {
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
                continue;
            }

            if (is_object($current) && property_exists($current, $segment)) {
                try {
                    $current = $current->{$segment};
                } catch (\Error) {
                    return null;
                }
                continue;
            }

            return null;
        }

        $exists = true;
        return $current;
    }

    /**
     * Determine whether a list already contains a value using strict comparison.
     *
     * Keeping this as a selector helper lets collection operations compare any
     * PHP value without serializing arrays or objects into lossy string keys.
     *
     * @param list<mixed> $values The values already encountered.
     * @param mixed       $needle The value to search for.
     * @return bool True when the value was already encountered.
     */
    private static function containsStrictValue(array $values, mixed $needle): bool
    {
        foreach ($values as $value) {
            if ($value === $needle) {
                return true;
            }
        }

        return false;
    }
}
