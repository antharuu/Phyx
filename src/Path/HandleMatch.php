<?php

declare(strict_types=1);

namespace Phyx\Path;

use Phyx\Enums\MatchMode;

/**
 * Pattern-matching helpers for {@see \Phyx\Path}.
 */
trait HandleMatch
{
    /**
     * Find pathnames matching a glob pattern.
     *
     * Searches for all the pathnames matching pattern according to the rules
     * used by the libc glob() function, which is similar to the rules used
     * by common shells. Normalizes the result to an empty list if no matches
     * are found or an error occurs.
     *
     * @param string $pattern The glob pattern to match.
     * @return list<string>   A list of matching paths.
     *
     * @example
     *   Path::glob('src/*.php'); // => ['src/Path.php', 'src/Arr.php']
     *
     * @see https://www.php.net/manual/en/function.glob.php PHP native glob()
     */
    public static function glob(string $pattern): array
    {
        return array_map('strval', glob($pattern) ?: []);
    }

    /**
     * Check if a path matches a given pattern.
     *
     * Supports matching via shell globs, regular expressions, or literal
     * string comparison based on the provided mode.
     *
     * @param string    $path    The path to test.
     * @param string    $pattern The pattern to match against.
     * @param MatchMode $mode    The matching strategy {@see MatchMode}. Defaults to MatchMode::Glob.
     * @return bool              True if the path matches the pattern.
     *
     * @example
     *   Path::matches('src/Path.php', 'src/*.php'); // => true
     *   Path::matches('src/Path.php', '/^src/', MatchMode::Regex); // => true
     *
     * @see \Phyx\Enums\MatchMode
     * @see https://www.php.net/manual/en/function.fnmatch.php PHP fnmatch()
     */
    public static function matches(string $path, string $pattern, MatchMode $mode = MatchMode::Glob): bool
    {
        return match ($mode) {
            MatchMode::Glob => fnmatch($pattern, $path),
            MatchMode::Regex => preg_match($pattern, $path) === 1,
            MatchMode::Literal => $path === $pattern,
        };
    }
}
