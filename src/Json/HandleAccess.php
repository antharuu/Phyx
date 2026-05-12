<?php

declare(strict_types=1);

namespace Phyx\Json;

trait HandleAccess
{
    /**
     * Retrieve a value from a JSON string by its path.
     *
     * Decodes the JSON into an associative array and traverses it using dot notation.
     *
     * @param string                            $json    The JSON string to search.
     * @param string|int|array<int, string|int> $path    The path to the value (e.g., 'users.0.name'). {@see \Phyx\Enums\JsonPathMode}
     * @param mixed                             $default The value to return if the path is not found.
     *
     * @return mixed The value at the specified path, or the default value.
     *
     * @throws \JsonException If the JSON is invalid.
     *
     * @example
     *   Json::get('{"a":{"b":1}}', 'a.b'); // => 1
     *   Json::get('{"a":1}', 'b', 2);      // => 2
     *
     * @see \Phyx\Json::decodeArray()
     */
    public static function get(string $json, string|int|array $path, mixed $default = null): mixed
    {
        $document = self::decodeArray($json);
        $found = false;
        $value = self::valueAtPath($document, self::pathSegments($path), $found);

        return $found ? $value : $default;
    }

    /**
     * Determine if a path exists within a JSON string.
     *
     * Checks if the specified path can be resolved to a value.
     *
     * @param string                            $json The JSON string to search.
     * @param string|int|array<int, string|int> $path The path to check. {@see \Phyx\Enums\JsonPathMode}
     *
     * @return bool True if the path exists, false otherwise.
     *
     * @throws \JsonException If the JSON is invalid.
     *
     * @example
     *   Json::has('{"a":1}', 'a'); // => true
     *   Json::has('{"a":1}', 'b'); // => false
     *
     * @see HandleAccess::get()
     */
    public static function has(string $json, string|int|array $path): bool
    {
        $document = self::decodeArray($json);
        $found = false;
        self::valueAtPath($document, self::pathSegments($path), $found);

        return $found;
    }

    /**
     * Set a value at a specific path within a JSON string.
     *
     * Updates an existing value or creates the path if it does not exist.
     *
     * @param string                            $json  The JSON string to modify.
     * @param string|int|array<int, string|int> $path  The path where the value should be set. {@see \Phyx\Enums\JsonPathMode}
     * @param mixed                             $value The value to set.
     *
     * @return string The modified JSON string.
     *
     * @throws \JsonException If the JSON is invalid.
     *
     * @example
     *   Json::set('{"a":1}', 'a', 2); // => '{"a":2}'
     *   Json::set('{}', 'a.b', 1);    // => '{"a":{"b":1}}'
     *
     * @see \Phyx\Json::encode()
     */
    public static function set(string $json, string|int|array $path, mixed $value): string
    {
        $document = self::decodeArray($json);
        $segments = self::pathSegments($path);

        if ($segments === []) {
            return self::encode($value);
        }

        self::setValueAtPath($document, $segments, $value);

        return self::encode($document);
    }

    /**
     * Remove a value at a specific path within a JSON string.
     *
     * Deletes the element at the path; if it is an element of a list, the list is re-indexed.
     *
     * @param string                            $json The JSON string to modify.
     * @param string|int|array<int, string|int> $path The path to remove. {@see \Phyx\Enums\JsonPathMode}
     *
     * @return string The modified JSON string.
     *
     * @throws \JsonException If the JSON is invalid.
     *
     * @example
     *   Json::remove('{"a":1,"b":2}', 'a'); // => '{"b":2}'
     *
     * @see \Phyx\Json::encode()
     */
    public static function remove(string $json, string|int|array $path): string
    {
        $document = self::decodeArray($json);
        $segments = self::pathSegments($path);

        if ($segments === []) {
            return self::encode(null);
        }

        self::removeValueAtPath($document, $segments);

        return self::encode($document);
    }

    /**
     * @param string|int|array<int, string|int> $path
     * @return list<string>
     */
    private static function pathSegments(string|int|array $path): array
    {
        if (is_array($path)) {
            $segments = array_values(array_map(static fn (string|int $segment): string => (string) $segment, $path));
        } else {
            $path = (string) $path;
            $segments = $path === '' ? [] : explode('.', $path);
        }

        return $segments;
    }

    /**
     * @param array<mixed> $document
     * @param list<string> $segments
     */
    private static function valueAtPath(array $document, array $segments, bool &$found): mixed
    {
        $current = $document;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                $found = false;
                return null;
            }

            $current = $current[$segment];
        }

        $found = true;

        return $current;
    }

    /**
     * @param array<mixed> $document
     * @param list<string> $segments
     */
    private static function setValueAtPath(array &$document, array $segments, mixed $value): void
    {
        $current =& $document;
        $last = array_pop($segments);
        assert($last !== null);

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }

            $current =& $current[$segment];
        }

        $current[$last] = $value;
    }

    /**
     * @param array<mixed> $document
     * @param list<string> $segments
     */
    private static function removeValueAtPath(array &$document, array $segments): void
    {
        $current =& $document;
        $last = array_pop($segments);
        assert($last !== null);

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                return;
            }

            $current =& $current[$segment];
        }

        if (array_key_exists($last, $current)) {
            $wasList = array_is_list($current);
            unset($current[$last]);

            if ($wasList) {
                $current = array_values($current);
            }
        }
    }
}
