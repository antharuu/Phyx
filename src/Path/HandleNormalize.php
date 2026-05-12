<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\PathStyle;

trait HandleNormalize
{
    /**
     * Normalize a path by resolving '..' and '.' segments.
     *
     * Simplifies the path by removing redundant segments. On Windows,
     * backslashes are used as separators. On Unix, forward slashes are used.
     * The style can be explicitly set or detected from the path.
     *
     * @param string    $path  The path to normalize.
     * @param PathStyle $style The path style to use {@see PathStyle}. Defaults to PathStyle::Native.
     * @return string          The normalized path.
     *
     * @example
     *   Path::normalize('usr/local/../bin'); // => 'usr/bin'
     *   Path::normalize('C:\\Windows\\.\\System32', PathStyle::Windows); // => 'C:\Windows\System32'
     */
    public static function normalize(string $path, PathStyle $style = PathStyle::Native): string
    {
        if ($style === PathStyle::Native) {
            $style = Support::detectStyle($path) === PathStyle::Windows ? PathStyle::Windows : PathStyle::Native;
        }

        return Support::normalize($path, $style);
    }

    /**
     * Normalize separators in a path according to the given style.
     *
     * Converts all slashes and backslashes to the appropriate separator
     * for the specified style. Does not resolve '..' or '.' segments.
     *
     * @param string    $path  The path to process.
     * @param PathStyle $style The path style to use {@see PathStyle}. Defaults to PathStyle::Native.
     * @return string          The path with normalized separators.
     *
     * @example
     *   Path::normalizeSeparators('usr/local/bin', PathStyle::Windows); // => 'usr\local\bin'
     *   Path::normalizeSeparators('C:\Windows', PathStyle::Unix); // => 'C:/Windows'
     */
    public static function normalizeSeparators(string $path, PathStyle $style = PathStyle::Native): string
    {
        return Support::normalizeSeparators($path, $style);
    }

    /**
     * Remove the trailing separator from a path.
     *
     * Removes any trailing slash or backslash unless the path represents
     * a root directory (e.g., '/' or 'C:\').
     *
     * @param string $path The path to trim.
     * @return string      The path without a trailing separator.
     *
     * @example
     *   Path::removeTrailingSeparator('/usr/bin/'); // => '/usr/bin'
     *   Path::removeTrailingSeparator('C:\\'); // => 'C:\'
     */
    public static function removeTrailingSeparator(string $path): string
    {
        return Support::removeTrailingSeparator($path);
    }

    /**
     * Ensure a path ends with a separator.
     *
     * Adds a trailing slash or backslash if it is missing, using the
     * detected path style.
     *
     * @param string $path The path to process.
     * @return string      The path with a trailing separator.
     *
     * @example
     *   Path::ensureTrailingSeparator('/usr/bin'); // => '/usr/bin/'
     *   Path::ensureTrailingSeparator('C:\\Windows'); // => 'C:\Windows\'
     */
    public static function ensureTrailingSeparator(string $path): string
    {
        if ($path === '') {
            return DIRECTORY_SEPARATOR;
        }

        if (str_ends_with($path, '/') || str_ends_with($path, '\\')) {
            return $path;
        }

        return $path . Support::separator(Support::detectStyle($path));
    }
}
