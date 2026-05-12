<?php

declare(strict_types=1);

namespace Phyx\Url;

trait HandleCompare
{
    /**
     * Determines if two URLs have the same origin.
     *
     * Compares the scheme, host, and effective port of both URLs. Origins are considered
     * identical if all three components match exactly (case-insensitive for scheme and host)
     * according to RFC 6454.
     *
     * @param  string  $a  The first URL to compare.
     * @param  string  $b  The second URL to compare.
     * @return bool True if origins match, false otherwise.
     *
     * @example Url::sameOrigin('https://example.com:443', 'https://example.com'); // => true
     * @example Url::sameOrigin('http://example.com', 'https://example.com'); // => false
     *
     * @see Url::sameHost()
     */
    public static function sameOrigin(string $a, string $b): bool
    {
        $left = self::parse($a);
        $right = self::parse($b);

        return strtolower((string) $left['scheme']) === strtolower((string) $right['scheme'])
            && self::sameHost($a, $b)
            && self::effectivePort($left['scheme'], $left['port']) === self::effectivePort($right['scheme'], $right['port']);
    }

    /**
     * Determines if two URLs have the same host.
     *
     * Compares the host components of both URLs case-insensitively.
     *
     * @param  string  $a  The first URL to compare.
     * @param  string  $b  The second URL to compare.
     * @return bool True if hosts match, false otherwise.
     *
     * @example Url::sameHost('https://EXAMPLE.com', 'https://example.com'); // => true
     * @example Url::sameHost('https://a.com', 'https://b.com'); // => false
     *
     * @see Url::host()
     */
    public static function sameHost(string $a, string $b): bool
    {
        $left = self::host($a);
        $right = self::host($b);

        return $left !== null && $right !== null && strtolower($left) === strtolower($right);
    }

    /**
     * Determines if two URLs have the same normalized path.
     *
     * Compares the path components of both URLs after applying path normalization
     * (e.g., resolving '.' and '..').
     *
     * @param  string  $a  The first URL to compare.
     * @param  string  $b  The second URL to compare.
     * @return bool True if paths match after normalization, false otherwise.
     *
     * @example Url::samePath('/a/b/../c', '/a/c'); // => true
     * @example Url::samePath('/a', '/b'); // => false
     *
     * @see Url::path(), Url::normalize()
     */
    public static function samePath(string $a, string $b): bool
    {
        $left = self::path($a) ?? '';
        $right = self::path($b) ?? '';

        return self::normalizePathSegments($left) === self::normalizePathSegments($right);
    }

    /**
     * Resolves the effective port for a given scheme and port.
     *
     * Returns the provided port if it's not null, otherwise returns the default
     * port for the given scheme (80 for http, 443 for https).
     *
     * @param  string|null  $scheme  The URL scheme.
     * @param  int|null     $port    The URL port.
     * @return int|null The effective port number.
     */
    private static function effectivePort(?string $scheme, ?int $port): ?int
    {
        if ($port !== null) {
            return $port;
        }

        return match (strtolower((string) $scheme)) {
            'http' => 80,
            'https' => 443,
            default => null,
        };
    }
}
