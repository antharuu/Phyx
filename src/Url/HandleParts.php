<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\UrlComponent;

trait HandleParts
{
    /**
     * Extracts the scheme component from a URL.
     *
     * Retrieves the protocol part of the URI (e.g., http, https, ftp)
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The scheme or null if missing.
     *
     * @example Url::scheme('https://example.com'); // => 'https'
     * @example Url::scheme('//example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Scheme
     */
    public static function scheme(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Scheme);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the user component from a URL.
     *
     * Retrieves the username part of the URI authority
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The username or null if missing.
     *
     * @example Url::user('https://alice@example.com'); // => 'alice'
     * @example Url::user('https://example.com'); // => null
     *
     * @see Url::component(), UrlComponent::User
     */
    public static function user(string $url): ?string
    {
        $value = self::component($url, UrlComponent::User);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the password component from a URL.
     *
     * Retrieves the password part of the URI authority
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The password or null if missing.
     *
     * @example Url::password('https://alice:secret@example.com'); // => 'secret'
     * @example Url::password('https://alice@example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Pass
     */
    public static function password(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Pass);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the host component from a URL.
     *
     * Retrieves the host part of the URI authority (domain or IP address)
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The host or null if missing.
     *
     * @example Url::host('https://example.com/path'); // => 'example.com'
     * @example Url::host('/path'); // => null
     *
     * @see Url::component(), UrlComponent::Host
     */
    public static function host(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Host);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the port component from a URL.
     *
     * Retrieves the port number of the URI authority
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return int|null The port number or null if missing.
     *
     * @example Url::port('https://example.com:8080'); // => 8080
     * @example Url::port('https://example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Port
     */
    public static function port(string $url): ?int
    {
        $value = self::component($url, UrlComponent::Port);

        return is_int($value) ? $value : null;
    }

    /**
     * Extracts the path component from a URL.
     *
     * Retrieves the path part of the URI
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The path or null if missing.
     *
     * @example Url::path('https://example.com/path/to/resource'); // => '/path/to/resource'
     * @example Url::path('https://example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Path
     */
    public static function path(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Path);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the query component from a URL.
     *
     * Retrieves the query string part of the URI (without the leading '?')
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The query string or null if missing.
     *
     * @example Url::query('https://example.com?foo=bar'); // => 'foo=bar'
     * @example Url::query('https://example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Query
     */
    public static function query(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Query);

        return is_string($value) ? $value : null;
    }

    /**
     * Extracts the fragment component from a URL.
     *
     * Retrieves the fragment part of the URI (without the leading '#')
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string.
     * @return string|null The fragment or null if missing.
     *
     * @example Url::fragment('https://example.com#section1'); // => 'section1'
     * @example Url::fragment('https://example.com'); // => null
     *
     * @see Url::component(), UrlComponent::Fragment
     */
    public static function fragment(string $url): ?string
    {
        $value = self::component($url, UrlComponent::Fragment);

        return is_string($value) ? $value : null;
    }
}
