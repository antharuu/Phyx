<?php

declare(strict_types=1);

namespace Phyx\Path;

trait HandleResolve
{
    /**
     * Resolve the absolute canonical path.
     *
     * Expands all symbolic links and resolves references to '/./', '/../'
     * and extra '/' characters in the input path. Returns null if the path
     * does not exist.
     *
     * @param string $path The path to resolve.
     * @return string|null The canonicalized absolute path, or null if it fails.
     *
     * @example
     *   Path::real('src/Path.php'); // => '/home/user/project/src/Path.php'
     *   Path::real('nonexistent.txt'); // => null
     *
     * @see https://www.php.net/manual/en/function.realpath.php PHP native realpath()
     */
    public static function real(string $path): ?string
    {
        $real = realpath($path);

        return $real === false ? null : $real;
    }

    /**
     * Check if a file or directory exists.
     *
     * Returns true if the specified path exists on the filesystem.
     *
     * @param string $path The path to check.
     * @return bool        True if the path exists.
     *
     * @example
     *   Path::exists('README.md'); // => true
     *
     * @see https://www.php.net/manual/en/function.file-exists.php PHP native file_exists()
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Check if the path refers to a regular file.
     *
     * Returns true if the path exists and is a regular file (not a directory).
     *
     * @param string $path The path to check.
     * @return bool        True if it is a file.
     *
     * @example
     *   Path::isFile('src/Path.php'); // => true
     *   Path::isFile('src/'); // => false
     *
     * @see https://www.php.net/manual/en/function.is-file.php PHP native is_file()
     */
    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Check if the path refers to a directory.
     *
     * Returns true if the path exists and is a directory.
     *
     * @param string $path The path to check.
     * @return bool        True if it is a directory.
     *
     * @example
     *   Path::isDirectory('src'); // => true
     *   Path::isDirectory('src/Path.php'); // => false
     *
     * @see https://www.php.net/manual/en/function.is-dir.php PHP native is_dir()
     */
    public static function isDirectory(string $path): bool
    {
        return is_dir($path);
    }
}
