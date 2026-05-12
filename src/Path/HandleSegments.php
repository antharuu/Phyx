<?php

declare(strict_types=1);

namespace Phyx\Path;

trait HandleSegments
{
    /**
     * Split a path into its individual segments.
     *
     * Breaks the path into an array of names, excluding the root component.
     * Redundant separators and '.' segments are removed during normalization.
     *
     * @param string $path The path to split.
     * @return list<string> A list of path segments.
     *
     * @example
     *   Path::segments('/usr/local/bin'); // => ['usr', 'local', 'bin']
     *   Path::segments('C:\\Windows\\System32'); // => ['Windows', 'System32']
     */
    public static function segments(string $path): array
    {
        return Support::rawSegments(self::normalize($path, Support::detectStyle($path)));
    }

    /**
     * Get the parent directory of a path.
     *
     * Returns the path to the directory containing the given file or directory.
     * Returns null if the path is a root or empty.
     *
     * @param string $path The path to inspect.
     * @return string|null The parent directory path, or null if none exists.
     *
     * @example
     *   Path::parent('/usr/local/bin'); // => '/usr/local'
     *   Path::parent('/'); // => null
     */
    public static function parent(string $path): ?string
    {
        $normalized = self::normalize($path, Support::detectStyle($path));
        if ($normalized === '' || self::isRoot($normalized)) {
            return null;
        }

        $parent = dirname($normalized);
        if ($parent === '.' && ! str_starts_with($normalized, '.')) {
            return null;
        }

        return $parent;
    }

    /**
     * Retrieve all parent directories of a path.
     *
     * Returns an array of paths for every parent directory, from the
     * immediate parent up to the root.
     *
     * @param string $path The path to inspect.
     * @return list<string> A list of ancestor directory paths.
     *
     * @example
     *   Path::ancestors('/usr/local/bin'); // => ['/usr/local', '/usr', '/']
     */
    public static function ancestors(string $path): array
    {
        $ancestors = [];
        $parent = self::parent($path);
        while ($parent !== null) {
            $ancestors[] = $parent;
            $parent = self::parent($parent);
        }

        return $ancestors;
    }

    /**
     * Get the last segment of a path.
     *
     * Identifies the final component of the path (the basename). Returns
     * null if the path contains no segments.
     *
     * @param string $path The path to inspect.
     * @return string|null The last segment, or null if empty.
     *
     * @example
     *   Path::lastSegment('/usr/local/bin'); // => 'bin'
     *   Path::lastSegment('/'); // => null
     */
    public static function lastSegment(string $path): ?string
    {
        $segments = self::segments($path);
        if ($segments === []) {
            return null;
        }

        return $segments[array_key_last($segments)];
    }
}
