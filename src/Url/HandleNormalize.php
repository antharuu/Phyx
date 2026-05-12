<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\QueryFormat;

trait HandleNormalize
{
    /**
     * Normalizes a URL to a canonical form.
     *
     * Applies standard normalization rules: converts scheme and host to lowercase,
     * removes default ports (80 for http, 443 for https), and collapses path
     * segments (e.g., '/a/./b/../c' to '/a/c') according to RFC 3986.
     *
     * @param  string  $url  The URL string to normalize.
     * @return string The normalized URL.
     *
     * @example Url::normalize('HTTPS://EXAMPLE.COM:443/foo/./bar/../'); // => 'https://example.com/foo/'
     * @example Url::normalize('https://example.com:443'); // => 'https://example.com'
     *
     * @see Url::removeDefaultPort()
     */
    public static function normalize(string $url): string
    {
        $parts = self::parse(self::removeDefaultPort($url));

        if ($parts['scheme'] !== null) {
            $parts['scheme'] = strtolower($parts['scheme']);
        }

        if ($parts['host'] !== null) {
            $parts['host'] = strtolower($parts['host']);
        }

        if ($parts['path'] !== null && $parts['path'] !== '') {
            $parts['path'] = self::normalizePathSegments($parts['path']);
        }

        return self::build($parts);
    }

    /**
     * Removes standard protocol ports from a URL.
     *
     * Strip port 80 if the scheme is 'http' and port 443 if the scheme is 'https',
     * as these are the default ports defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string The URL without default ports.
     *
     * @example Url::removeDefaultPort('http://example.com:80'); // => 'http://example.com'
     * @example Url::removeDefaultPort('https://example.com:443'); // => 'https://example.com'
     *
     * @see Url::normalize()
     */
    public static function removeDefaultPort(string $url): string
    {
        $parts = self::parse($url);
        $scheme = $parts['scheme'] === null ? null : strtolower($parts['scheme']);

        if (($scheme === 'http' && $parts['port'] === 80) || ($scheme === 'https' && $parts['port'] === 443)) {
            $parts['port'] = null;
        }

        return self::build($parts);
    }

    /**
     * Sorts the query parameters of a URL alphabetically.
     *
     * Parses the query string, sorts parameters by key, and rebuilds the URL
     * using the specified format.
     *
     * @param  string       $url     The URL string.
     * @param  QueryFormat  $format  The encoding format {@see QueryFormat}. Defaults to RFC 1738.
     * @return string The URL with sorted query parameters.
     *
     * @example Url::sortQuery('https://example.com?b=2&a=1'); // => 'https://example.com?a=1&b=2'
     *
     * @see Url::queryParameters(), Url::withQuery()
     */
    public static function sortQuery(string $url, QueryFormat $format = QueryFormat::Rfc1738): string
    {
        $parameters = self::queryParameters($url);
        ksort($parameters);

        return self::withQuery($url, $parameters, $format);
    }

    private static function normalizePathSegments(string $path): string
    {
        $absolute = str_starts_with($path, '/');
        $trailing = str_ends_with($path, '/');
        $segments = explode('/', $path);
        $stack = [];

        foreach ($segments as $segment) {
            self::pushNormalizedPathSegment($stack, $segment, $absolute);
        }

        $normalized = implode('/', $stack);

        if ($absolute) {
            $normalized = '/' . $normalized;
        }

        if ($trailing && $normalized !== '/') {
            $normalized .= '/';
        }

        return $normalized === '' && $absolute ? '/' : $normalized;
    }

    /** @param list<string> $stack */
    private static function pushNormalizedPathSegment(array &$stack, string $segment, bool $absolute): void
    {
        if ($segment === '' || $segment === '.') {
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

        if (! $absolute) {
            $stack[] = '..';
        }
    }
}
