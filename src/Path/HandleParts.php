<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\PathPart;

trait HandleParts
{
    /**
     * Retrieve information about a file path.
     *
     * Returns an associative array or a specific string component containing
     * 'dirname', 'basename', 'extension', and 'filename'.
     *
     * @param string        $path The path to parse.
     * @param PathPart|null $part The specific part to return {@see PathPart}. Defaults to null (returns all).
     * @return array<string, string>|string|null The path information or a specific part.
     *
     * @example
     *   Path::info('/usr/bin/php.ini'); // => ['dirname' => '/usr/bin', ...]
     *   Path::info('/usr/bin/php.ini', PathPart::Extension); // => 'ini'
     *
     * @see \Phyx\Enums\PathPart
     * @see https://www.php.net/manual/en/function.pathinfo.php PHP native pathinfo()
     */
    public static function info(string $path, ?PathPart $part = null): array|string|null
    {
        $info = pathinfo($path);
        /** @var array<string, string> $result */
        $result = [];
        foreach (['dirname', 'basename', 'extension', 'filename'] as $key) {
            if (isset($info[$key]) && $info[$key] !== '') {
                $result[$key] = (string) $info[$key];
            }
        }

        return match ($part) {
            PathPart::Dirname => $result['dirname'] ?? null,
            PathPart::Basename => $result['basename'] ?? null,
            PathPart::Filename => $result['filename'] ?? null,
            PathPart::Extension => $result['extension'] ?? null,
            null => $result,
        };
    }

    /**
     * Get the directory name component of a path.
     *
     * Returns the parent directory's path. Can traverse multiple levels up
     * the directory tree.
     *
     * @param string $path   The path to inspect.
     * @param int    $levels The number of parent directories to go up. Defaults to 1.
     * @return string        The name of the directory.
     *
     * @example
     *   Path::dirname('/usr/local/bin'); // => '/usr/local'
     *   Path::dirname('/usr/local/bin', 2); // => '/usr'
     *
     * @see https://www.php.net/manual/en/function.dirname.php PHP native dirname()
     */
    public static function dirname(string $path, int $levels = 1): string
    {
        return dirname($path, max(1, $levels));
    }

    /**
     * Get the trailing name component of a path.
     *
     * Returns the last component of a path (the file name or the last
     * directory name). An optional suffix can be removed from the result.
     *
     * @param string $path   The path to inspect.
     * @param string $suffix An optional suffix to strip.
     * @return string        The trailing name component.
     *
     * @example
     *   Path::basename('/usr/local/bin/php.ini'); // => 'php.ini'
     *   Path::basename('/usr/local/bin/php.ini', '.ini'); // => 'php'
     *
     * @see https://www.php.net/manual/en/function.basename.php PHP native basename()
     */
    public static function basename(string $path, string $suffix = ''): string
    {
        return basename($path, $suffix);
    }

    /**
     * Get the filename without the extension.
     *
     * Extracts the name of the file from a path, excluding any directory
     * information and the file extension.
     *
     * @param string $path The path to inspect.
     * @return string      The filename.
     *
     * @example
     *   Path::filename('/usr/bin/php.ini'); // => 'php'
     *   Path::filename('src/Path.php'); // => 'Path'
     */
    public static function filename(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        if ($filename === '' && self::basename($path) !== '') {
            return self::basename($path);
        }

        return $filename;
    }

    /**
     * Get the extension of a file path.
     *
     * Returns the file extension (the part after the last dot in the
     * basename). Returns null if no extension is found.
     *
     * @param string $path The path to inspect.
     * @return string|null The extension, or null if none exists.
     *
     * @example
     *   Path::extension('/usr/bin/php.ini'); // => 'ini'
     *   Path::extension('.gitignore'); // => null
     */
    public static function extension(string $path): ?string
    {
        $basename = self::basename($path);
        if (str_starts_with($basename, '.') && substr_count($basename, '.') === 1) {
            return null;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return $extension === '' ? null : $extension;
    }
}
