<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\PathRoot;
use Phyx\Enums\PathStyle;

final class Support
{
    /**
     * Get the directory separator for a given path style.
     *
     * Returns '\' for Windows and '/' for Unix.
     *
     * @param PathStyle $style The style to use {@see PathStyle}.
     * @return string          The directory separator.
     *
     * @example
     *   Support::separator(PathStyle::Unix); // => '/'
     *   Support::separator(PathStyle::Windows); // => '\'
     */
    public static function separator(PathStyle $style): string
    {
        return match ($style) {
            PathStyle::Windows => '\\',
            PathStyle::Unix => '/',
            PathStyle::Native => DIRECTORY_SEPARATOR,
        };
    }

    /**
     * Detect the path style based on separators and root format.
     *
     * Identifies Windows style if backslashes or drive letters are present,
     * otherwise defaults to Unix.
     *
     * @param string $path The path to analyze.
     * @return PathStyle   The detected style {@see PathStyle}.
     *
     * @example
     *   Support::detectStyle('C:\Windows'); // => PathStyle::Windows
     *   Support::detectStyle('/usr/bin'); // => PathStyle::Unix
     */
    public static function detectStyle(string $path): PathStyle
    {
        if (str_contains($path, '\\') || preg_match('/^[A-Za-z]:/', $path) === 1) {
            return PathStyle::Windows;
        }

        return PathStyle::Unix;
    }

    /**
     * Normalize separators in a path according to the given style.
     *
     * Converts all slashes to the style-appropriate separator. Handles
     * UNC paths on Windows correctly.
     *
     * @param string    $path  The path to process.
     * @param PathStyle $style The target style {@see PathStyle}.
     * @return string          The path with normalized separators.
     *
     * @example
     *   Support::normalizeSeparators('usr/local/bin', PathStyle::Windows); // => 'usr\local\bin'
     */
    public static function normalizeSeparators(string $path, PathStyle $style): string
    {
        $isUnc = str_starts_with($path, '\\\\') || str_starts_with($path, '//');
        $unix = preg_replace('#/+#', '/', str_replace('\\', '/', $path)) ?? $path;
        if (self::separator($style) === '/') {
            return $isUnc && ! str_starts_with($unix, '//') ? '/' . $unix : $unix;
        }

        $windows = str_replace('/', '\\', $unix);
        return $isUnc && ! str_starts_with($windows, '\\\\') ? '\\' . $windows : $windows;
    }

    /**
     * Extract detailed root information from a path.
     *
     * Returns an array containing the root type, its normalized value,
     * and the raw match.
     *
     * @param string $path The path to inspect.
     * @return array{type: PathRoot, value: ?string, raw: ?string} Root metadata.
     *
     * @see \Phyx\Enums\PathRoot
     */
    public static function rootInfo(string $path): array
    {
        $info = ['type' => PathRoot::None, 'value' => null, 'raw' => null];

        if (preg_match('#^[\\\\/]{2}([^\\\\/]+)[\\\\/]([^\\\\/]+)#', $path, $matches) === 1) {
            $info = ['type' => PathRoot::Unc, 'value' => '\\\\' . $matches[1] . '\\' . $matches[2], 'raw' => $matches[0]];
        } elseif (preg_match('#^([A-Za-z]):[\\\\/]#', $path, $matches) === 1) {
            $info = ['type' => PathRoot::WindowsDrive, 'value' => strtoupper($matches[1]) . ':\\', 'raw' => $matches[0]];
        } elseif (str_starts_with($path, '/')) {
            $info = ['type' => PathRoot::Unix, 'value' => '/', 'raw' => '/'];
        }

        return $info;
    }

    /**
     * Get the root portion of a path formatted for a specific style.
     *
     * @param string    $path  The path to inspect.
     * @param PathStyle $style The target style {@see PathStyle}.
     * @return string|null     The formatted root, or null if relative.
     */
    public static function rootForStyle(string $path, PathStyle $style): ?string
    {
        $info = self::rootInfo($path);
        if ($info['value'] === null) {
            return null;
        }

        return self::normalizeSeparators($info['value'], $style);
    }

    /**
     * Remove the root component from a path.
     *
     * @param string $path The path to strip.
     * @return string      The path without its root.
     */
    public static function stripRoot(string $path): string
    {
        $info = self::rootInfo($path);
        if ($info['raw'] === null) {
            return $path;
        }

        return substr($path, strlen($info['raw']));
    }

    /**
     * Split a path into segments without normalization.
     *
     * @param string $path The path to split.
     * @return list<string> The raw path segments.
     */
    public static function rawSegments(string $path): array
    {
        $path = self::stripRoot($path);
        $path = str_replace('\\', '/', $path);
        if ($path === '') {
            return [];
        }

        $parts = preg_split('#/+#', $path, -1, PREG_SPLIT_NO_EMPTY);

        return $parts === false ? [] : array_map('strval', $parts);
    }

    /**
     * Normalize a path according to the specified style.
     *
     * Resolves '.' and '..' segments and ensures consistent separators.
     *
     * @param string    $path  The path to normalize.
     * @param PathStyle $style The target style {@see PathStyle}.
     * @return string          The normalized path.
     */
    public static function normalize(string $path, PathStyle $style): string
    {
        if ($path === '') {
            return '';
        }

        $root = self::rootForStyle($path, $style);
        $stack = self::normalizedSegments(self::rawSegments($path), self::rootInfo($path)['type'] === PathRoot::None);

        return self::buildNormalizedPath($root, $stack, self::separator($style));
    }

    /**
     * @param list<string> $segments
     * @return list<string>
     */
    private static function normalizedSegments(array $segments, bool $allowLeadingParent): array
    {
        $stack = [];

        foreach ($segments as $segment) {
            self::pushNormalizedSegment($stack, $segment, $allowLeadingParent);
        }

        return $stack;
    }

    /** @param list<string> $stack */
    private static function pushNormalizedSegment(array &$stack, string $segment, bool $allowLeadingParent): void
    {
        if ($segment === '.' || $segment === '') {
            return;
        }

        if ($segment !== '..') {
            $stack[] = $segment;
            return;
        }

        if ($stack !== [] && end($stack) !== '..') {
            array_pop($stack);
            return;
        }

        if ($allowLeadingParent) {
            $stack[] = '..';
        }
    }

    /** @param list<string> $stack */
    private static function buildNormalizedPath(?string $root, array $stack, string $separator): string
    {
        if ($root !== null) {
            return $stack === [] ? $root : rtrim($root, '\\/') . $separator . implode($separator, $stack);
        }

        return implode($separator, $stack);
    }

    /**
     * Remove the trailing separator from a path.
     *
     * Does not remove the separator if it is part of the root.
     *
     * @param string $path The path to trim.
     * @return string      The path without a trailing separator.
     */
    public static function removeTrailingSeparator(string $path): string
    {
        $root = self::rootInfo($path);
        if ($root['value'] !== null && self::samePath(self::normalizeSeparators($path, self::detectStyle($path)), $root['value'])) {
            return self::normalizeSeparators($root['value'], self::detectStyle($path));
        }

        return rtrim($path, '\\/');
    }

    /**
     * Check if two paths are semantically the same.
     *
     * Performs a case-insensitive comparison and ignores separator differences.
     *
     * @param string $a The first path.
     * @param string $b The second path.
     * @return bool     True if the paths are equivalent.
     */
    public static function samePath(string $a, string $b): bool
    {
        return strtolower(str_replace('/', '\\', $a)) === strtolower(str_replace('/', '\\', $b));
    }
}
