<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\PathStyle;

trait HandleTransform
{
    /**
     * Change the file extension of a path.
     *
     * Replaces the current extension with a new one. If the path has no
     * extension, the new one is appended.
     *
     * @param string $path      The path to modify.
     * @param string $extension The new extension (with or without a leading dot).
     * @return string           The path with the new extension.
     *
     * @example
     *   Path::withExtension('src/Path.php', 'txt'); // => 'src/Path.txt'
     *   Path::withExtension('src/Path', '.php'); // => 'src/Path.php'
     */
    public static function withExtension(string $path, string $extension): string
    {
        $extension = ltrim($extension, '.');
        $without = self::withoutExtension($path);

        return $extension === '' ? $without : $without . '.' . $extension;
    }

    /**
     * Remove the file extension from a path.
     *
     * Returns the path without its trailing extension and the preceding dot.
     *
     * @param string $path The path to modify.
     * @return string      The path without an extension.
     *
     * @example
     *   Path::withoutExtension('src/Path.php'); // => 'src/Path'
     *   Path::withoutExtension('src/Path'); // => 'src/Path'
     */
    public static function withoutExtension(string $path): string
    {
        $extension = self::extension($path);
        if ($extension === null) {
            return $path;
        }

        return substr($path, 0, -strlen($extension) - 1);
    }

    /**
     * Convert a path to Unix style.
     *
     * Replaces all backslashes with forward slashes and ensures the
     * formatting matches Unix conventions.
     *
     * @param string $path The path to convert.
     * @return string      The Unix-style path.
     *
     * @example
     *   Path::toUnix('C:\\Windows\\System32'); // => 'C:/Windows/System32'
     *
     * @see PathStyle::Unix
     */
    public static function toUnix(string $path): string
    {
        return self::normalizeSeparators($path, PathStyle::Unix);
    }

    /**
     * Convert a path to Windows style.
     *
     * Replaces all forward slashes with backslashes and ensures the
     * formatting matches Windows conventions.
     *
     * @param string $path The path to convert.
     * @return string      The Windows-style path.
     *
     * @example
     *   Path::toWindows('/usr/local/bin'); // => '\\usr\\local\\bin'
     *
     * @see PathStyle::Windows
     */
    public static function toWindows(string $path): string
    {
        return self::normalizeSeparators($path, PathStyle::Windows);
    }

    /**
     * Calculate the relative path from one location to another.
     *
     * Determines the sequence of directory changes needed to go from the
     * base path to the target path. If the paths are on different roots
     * (e.g., different drives on Windows), the absolute target path is returned.
     *
     * @param string $path The target path.
     * @param string $base The starting path.
     * @return string      The relative path.
     *
     * @example
     *   Path::relative('/usr/local/bin', '/usr/bin'); // => '../local/bin'
     *   Path::relative('C:\\Windows', 'C:\\Users'); // => '..\\Windows'
     */
    public static function relative(string $path, string $base): string
    {
        $style = Support::detectStyle($path . $base);
        $pathNormalized = self::normalize($path, $style);
        $baseNormalized = self::normalize($base, $style);
        $pathRoot = self::root($pathNormalized);
        $baseRoot = self::root($baseNormalized);
        if (($pathRoot ?? '') !== ($baseRoot ?? '') && ! Support::samePath($pathRoot ?? '', $baseRoot ?? '')) {
            return $pathNormalized;
        }

        $pathSegments = self::segments($pathNormalized);
        $baseSegments = self::segments($baseNormalized);
        $common = 0;
        $max = min(count($pathSegments), count($baseSegments));
        while ($common < $max && (($style === PathStyle::Windows && strtolower($pathSegments[$common]) === strtolower($baseSegments[$common])) || $pathSegments[$common] === $baseSegments[$common])) {
            ++$common;
        }

        $relative = array_merge(
            array_fill(0, count($baseSegments) - $common, '..'),
            array_slice($pathSegments, $common),
        );

        return implode(Support::separator($style), $relative);
    }
}
