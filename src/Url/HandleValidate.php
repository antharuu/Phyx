<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\UrlValidation;

trait HandleValidate
{
    /**
     * Validates a URL against a specific criteria.
     *
     * Checks if the given string is a valid URL according to the specified
     * validation mode (absolute, relative, or HTTP/HTTPS).
     *
     * @param  string         $url         The URL string to validate.
     * @param  UrlValidation  $validation  The validation mode {@see UrlValidation}. Defaults to Absolute.
     * @return bool True if valid, false otherwise.
     *
     * @example Url::isValid('https://example.com'); // => true
     * @example Url::isValid('/path', UrlValidation::Relative); // => true
     * @example Url::isValid('https://example.com', UrlValidation::Http); // => true
     *
     * @see Url::isAbsolute(), Url::isRelative(), Url::isHttp()
     */
    public static function isValid(string $url, UrlValidation $validation = UrlValidation::Absolute): bool
    {
        return match ($validation) {
            UrlValidation::Absolute => self::isAbsolute($url),
            UrlValidation::Relative => self::isRelative($url),
            UrlValidation::Http => self::isHttp($url) || self::isHttps($url),
        };
    }

    /**
     * Checks if a URL is absolute.
     *
     * A URL is considered absolute if it has a non-empty scheme component
     * as defined in RFC 3986.
     *
     * @param  string  $url  The URL string to check.
     * @return bool True if absolute, false otherwise.
     *
     * @example Url::isAbsolute('https://example.com'); // => true
     * @example Url::isAbsolute('/path'); // => false
     *
     * @see Url::scheme()
     */
    public static function isAbsolute(string $url): bool
    {
        $parts = self::tryParse($url);

        return $parts !== null && $parts['scheme'] !== null && $parts['scheme'] !== '';
    }

    /**
     * Checks if a URL is relative.
     *
     * A URL is considered relative if it does not have a scheme or host component,
     * and does not start with a protocol-relative '//' prefix.
     *
     * @param  string  $url  The URL string to check.
     * @return bool True if relative, false otherwise.
     *
     * @example Url::isRelative('/path/to/resource'); // => true
     * @example Url::isRelative('https://example.com'); // => false
     *
     * @see Url::isAbsolute()
     */
    public static function isRelative(string $url): bool
    {
        $parts = self::tryParse($url);

        return $parts !== null && $parts['scheme'] === null && $parts['host'] === null && ! str_starts_with($url, '//');
    }

    /**
     * Checks if a URL uses the HTTP protocol.
     *
     * Validates that the URL has an 'http' scheme and a valid host component.
     *
     * @param  string  $url  The URL string to check.
     * @return bool True if HTTP, false otherwise.
     *
     * @example Url::isHttp('http://example.com'); // => true
     * @example Url::isHttp('https://example.com'); // => false
     *
     * @see Url::isHttps()
     */
    public static function isHttp(string $url): bool
    {
        $parts = self::tryParse($url);

        return $parts !== null && strtolower((string) $parts['scheme']) === 'http' && $parts['host'] !== null;
    }

    /**
     * Checks if a URL uses the HTTPS protocol.
     *
     * Validates that the URL has an 'https' scheme and a valid host component.
     *
     * @param  string  $url  The URL string to check.
     * @return bool True if HTTPS, false otherwise.
     *
     * @example Url::isHttps('https://example.com'); // => true
     * @example Url::isHttps('http://example.com'); // => false
     *
     * @see Url::isHttp()
     */
    public static function isHttps(string $url): bool
    {
        $parts = self::tryParse($url);

        return $parts !== null && strtolower((string) $parts['scheme']) === 'https' && $parts['host'] !== null;
    }
}
