<?php

declare(strict_types=1);

namespace Phyx\Path;

trait HandleJoin
{
    /**
     * Join multiple path segments into a single path.
     *
     * Combines multiple strings into a single path using the appropriate separator
     * for the detected style. Empty strings are ignored. The resulting path is
     * normalized to resolve '..' and '.' segments.
     *
     * @param string ...$paths Path segments to join.
     * @return string          The joined and normalized path.
     *
     * @example
     *   Path::join('src', 'Path.php'); // => 'src/Path.php'
     *   Path::join('/usr', 'local', 'bin'); // => '/usr/local/bin'
     *   Path::join('C:\\', 'Windows', 'System32'); // => 'C:\Windows\System32'
     */
    public static function join(string ...$paths): string
    {
        $paths = array_values(array_filter($paths, static fn (string $path): bool => $path !== ''));
        if ($paths === []) {
            return '';
        }

        $style = Support::detectStyle(implode('', $paths));
        $separator = Support::separator($style);
        $first = array_shift($paths);
        $joined = $first;
        foreach ($paths as $path) {
            $joined = rtrim($joined, '\\/') . $separator . ltrim($path, '\\/');
        }

        return self::normalize($joined, $style);
    }

    /**
     * Append segments to an existing path.
     *
     * Alias for {@see join()} where the first argument is the base path.
     *
     * @param string    $path     The base path.
     * @param string ...$segments Segments to append.
     * @return string             The resulting path.
     *
     * @example
     *   Path::append('/usr', 'bin'); // => '/usr/bin'
     */
    public static function append(string $path, string ...$segments): string
    {
        return self::join($path, ...$segments);
    }

    /**
     * Prepend a base path to an existing path.
     *
     * Alias for {@see join()} where the first argument is the new base.
     *
     * @param string $path The path to be prepended to.
     * @param string $base The base path to prepend.
     * @return string      The resulting path.
     *
     * @example
     *   Path::prepend('bin', '/usr'); // => '/usr/bin'
     */
    public static function prepend(string $path, string $base): string
    {
        return self::join($base, $path);
    }
}
