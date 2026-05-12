<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\PathRoot;
use Phyx\Enums\PathStyle;

trait HandleInspect
{
    /**
     * Determine if a path is absolute.
     *
     * An absolute path contains enough information to locate a file or directory
     * without reference to a current working directory. On Windows, this includes
     * drive letters (C:\) and UNC paths (\\server\share). On Unix, it starts with
     * a forward slash (/).
     *
     * @param string $path The path to check.
     * @return bool        True if the path is absolute.
     *
     * @example
     *   Path::isAbsolute('/usr/bin'); // => true
     *   Path::isAbsolute('C:\\Windows'); // => true
     *   Path::isAbsolute('src/Path.php'); // => false
     *
     * @see \Phyx\Enums\PathRoot
     */
    public static function isAbsolute(string $path): bool
    {
        return Support::rootInfo($path)['type'] !== PathRoot::None;
    }

    /**
     * Determine if a path is relative.
     *
     * A relative path depends on the current working directory to locate a file or
     * directory. It does not start with a root element like a drive letter or
     * a forward slash.
     *
     * @param string $path The path to check.
     * @return bool        True if the path is relative.
     *
     * @example
     *   Path::isRelative('src/Path.php'); // => true
     *   Path::isRelative('/usr/bin'); // => false
     */
    public static function isRelative(string $path): bool
    {
        return ! self::isAbsolute($path);
    }

    /**
     * Determine if a path refers to a root directory.
     *
     * A path is considered a root if it represents the top-level directory of a
     * filesystem or drive. Trailing separators are ignored during comparison.
     *
     * @param string $path The path to check.
     * @return bool        True if the path is a root.
     *
     * @example
     *   Path::isRoot('/'); // => true
     *   Path::isRoot('C:\\'); // => true
     *   Path::isRoot('/home'); // => false
     */
    public static function isRoot(string $path): bool
    {
        $root = self::root($path);
        return $root !== null && Support::samePath(Support::removeTrailingSeparator($path), Support::removeTrailingSeparator($root));
    }

    /**
     * Extract the root portion of a path.
     *
     * Identifies the root component of a path, such as '/' for Unix, 'C:\' for Windows
     * drive letters, or '\\server\share' for UNC paths. Returns null if no root
     * is found (relative path).
     *
     * @param string $path The path to inspect.
     * @return string|null The root portion, or null if relative.
     *
     * @example
     *   Path::root('/usr/local/bin'); // => '/'
     *   Path::root('C:\\Windows\\System32'); // => 'C:\'
     *   Path::root('src/Path.php'); // => null
     */
    public static function root(string $path): ?string
    {
        return Support::rootInfo($path)['value'];
    }

    /**
     * Detect the path style based on separators and root format.
     *
     * Automatically identifies whether a path follows Windows or Unix conventions
     * by looking for backslashes, drive letters, or UNC prefixes. Defaults to
     * Unix if the style is ambiguous.
     *
     * @param string $path The path to analyze.
     * @return PathStyle   The detected style {@see PathStyle}.
     *
     * @example
     *   Path::style('C:\\Windows'); // => PathStyle::Windows
     *   Path::style('/usr/bin'); // => PathStyle::Unix
     *
     * @see \Phyx\Enums\PathStyle
     */
    public static function style(string $path): PathStyle
    {
        return Support::detectStyle($path);
    }
}
