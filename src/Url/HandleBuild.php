<?php

declare(strict_types=1);

namespace Phyx\Url;

trait HandleBuild
{
    /**
     * Constructs a URL from its constituent components.
     *
     * Assembles a URI string from an array of parts (scheme, user, pass, host, port, path, query, fragment)
     * according to RFC 3986. Correctly handles authority delimiters and prefixes.
     *
     * @param  array<string, mixed>  $parts  Array of URL components.
     * @return string The constructed URL.
     *
     * @example Url::build(['scheme' => 'https', 'host' => 'example.com', 'path' => '/search']);
     *          // => 'https://example.com/search'
     * @example Url::build(['host' => 'localhost', 'port' => 8000]);
     *          // => '//localhost:8000'
     *
     * @see Url::parse()
     */
    public static function build(array $parts): string
    {
        $scheme = self::nullablePart($parts, 'scheme');
        $host = self::nullablePart($parts, 'host');

        return self::buildUrlPrefix($scheme, $host)
            . self::buildAuthority($parts, $host)
            . (self::nullablePart($parts, 'path') ?? '')
            . self::buildSuffix('?', self::nullablePart($parts, 'query'))
            . self::buildSuffix('#', self::nullablePart($parts, 'fragment'));
    }

    /** @param array<string, mixed> $parts */
    private static function nullablePart(array $parts, string $key): ?string
    {
        return array_key_exists($key, $parts) && $parts[$key] !== null ? (string) $parts[$key] : null;
    }

    private static function buildUrlPrefix(?string $scheme, ?string $host): string
    {
        if ($scheme !== null) {
            return $scheme . ($host !== null ? '://' : ':');
        }

        return $host !== null ? '//' : '';
    }

    /** @param array<string, mixed> $parts */
    private static function buildAuthority(array $parts, ?string $host): string
    {
        if ($host === null) {
            return '';
        }

        $port = array_key_exists('port', $parts) && $parts['port'] !== null ? ':' . (int) $parts['port'] : '';

        return self::buildUserInfo(self::nullablePart($parts, 'user'), self::nullablePart($parts, 'pass')) . $host . $port;
    }

    private static function buildUserInfo(?string $user, ?string $pass): string
    {
        if ($user === null) {
            return '';
        }

        return $user . ($pass !== null ? ':' . $pass : '') . '@';
    }

    private static function buildSuffix(string $prefix, ?string $value): string
    {
        return $value === null ? '' : $prefix . $value;
    }

    /**
     * Updates the scheme component of a URL.
     *
     * Parses the URL, replaces its scheme, and rebuilds the string
     * according to RFC 3986.
     *
     * @param  string  $url     The original URL string.
     * @param  string  $scheme  The new scheme (e.g., 'https').
     * @return string The updated URL.
     *
     * @example Url::withScheme('http://example.com', 'https'); // => 'https://example.com'
     *
     * @see Url::scheme(), Url::build()
     */
    public static function withScheme(string $url, string $scheme): string
    {
        $parts = self::parse($url);
        $parts['scheme'] = $scheme;

        return self::build($parts);
    }

    /**
     * Updates the host component of a URL.
     *
     * Parses the URL, replaces its host, and rebuilds the string
     * according to RFC 3986.
     *
     * @param  string  $url   The original URL string.
     * @param  string  $host  The new host name.
     * @return string The updated URL.
     *
     * @example Url::withHost('https://old.com', 'new.com'); // => 'https://new.com'
     *
     * @see Url::host(), Url::build()
     */
    public static function withHost(string $url, string $host): string
    {
        $parts = self::parse($url);
        $parts['host'] = $host;

        return self::build($parts);
    }

    /**
     * Updates the path component of a URL.
     *
     * Parses the URL, replaces its path, and rebuilds the string
     * according to RFC 3986.
     *
     * @param  string  $url   The original URL string.
     * @param  string  $path  The new path string.
     * @return string The updated URL.
     *
     * @example Url::withPath('https://example.com/old', '/new'); // => 'https://example.com/new'
     *
     * @see Url::path(), Url::build()
     */
    public static function withPath(string $url, string $path): string
    {
        $parts = self::parse($url);
        $parts['path'] = $path;

        return self::build($parts);
    }

    /**
     * Updates the fragment component of a URL.
     *
     * Parses the URL, replaces its fragment, and rebuilds the string
     * according to RFC 3986.
     *
     * @param  string  $url       The original URL string.
     * @param  string  $fragment  The new fragment string.
     * @return string The updated URL.
     *
     * @example Url::withFragment('https://example.com#old', 'new'); // => 'https://example.com#new'
     *
     * @see Url::fragment(), Url::build()
     */
    public static function withFragment(string $url, string $fragment): string
    {
        $parts = self::parse($url);
        $parts['fragment'] = $fragment;

        return self::build($parts);
    }

    /**
     * Removes the fragment component from a URL.
     *
     * Parses the URL, clears its fragment, and rebuilds the string
     * according to RFC 3986.
     *
     * @param  string  $url  The original URL string.
     * @return string The updated URL.
     *
     * @example Url::withoutFragment('https://example.com#section'); // => 'https://example.com'
     *
     * @see Url::fragment(), Url::build()
     */
    public static function withoutFragment(string $url): string
    {
        $parts = self::parse($url);
        $parts['fragment'] = null;

        return self::build($parts);
    }
}
