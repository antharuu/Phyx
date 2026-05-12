<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\QueryFormat;

trait HandleQuery
{
    /**
     * Parses a query string into an associative array.
     *
     * Decodes a URL-encoded query string into its parameter pairs.
     * Handles leading '?' and applies URL decoding to keys and values.
     *
     * @param  string  $query  The query string to parse.
     * @return array<string, mixed> Associative array of parameters.
     *
     * @example Url::parseQuery('foo=bar&baz=qux'); // => ['foo' => 'bar', 'baz' => 'qux']
     * @example Url::parseQuery('?page=1&limit=10'); // => ['page' => '1', 'limit' => '10']
     *
     * @see parse_str()
     */
    public static function parseQuery(string $query): array
    {
        $query = ltrim($query, '?');

        if ($query === '') {
            return [];
        }

        /** @var array<string, mixed> $parameters */
        $parameters = [];
        parse_str($query, $parameters);

        /** @var array<string, mixed> $safeParameters */
        $safeParameters = [];
        foreach ($parameters as $key => $value) {
            if (is_string($key)) {
                $safeParameters[$key] = $value;
            }
        }

        return $safeParameters;
    }

    /**
     * Builds a URL-encoded query string from parameters.
     *
     * Encodes an associative array of parameters into a query string
     * using the specified format (RFC 1738 or RFC 3986).
     *
     * @param  array<string, mixed>  $parameters  Associative array of parameters.
     * @param  QueryFormat           $format      The encoding format {@see QueryFormat}. Defaults to RFC 1738.
     * @return string The constructed query string.
     *
     * @example Url::buildQuery(['a' => 1, 'b' => 2]); // => 'a=1&b=2'
     * @example Url::buildQuery(['name' => 'John Doe'], QueryFormat::Rfc3986); // => 'name=John%20Doe'
     *
     * @see http_build_query()
     */
    public static function buildQuery(array $parameters, QueryFormat $format = QueryFormat::Rfc1738): string
    {
        return http_build_query($parameters, '', '&', self::queryEncoding($format));
    }

    /**
     * Retrieves the query parameters from a URL.
     *
     * Parses the query component of the given URL and returns it as an associative array.
     *
     * @param  string  $url  The URL string.
     * @return array<string, mixed> Associative array of parameters.
     *
     * @example Url::queryParameters('https://example.com?foo=bar'); // => ['foo' => 'bar']
     * @example Url::queryParameters('https://example.com'); // => []
     *
     * @see Url::query(), Url::parseQuery()
     */
    public static function queryParameters(string $url): array
    {
        $query = self::query($url);

        return $query === null ? [] : self::parseQuery($query);
    }

    /**
     * Updates the query component of a URL with new parameters.
     *
     * Replaces the entire query string of the URL with a new one
     * constructed from the provided parameters.
     *
     * @param  string                $url         The original URL string.
     * @param  array<string, mixed>  $parameters  The new set of parameters.
     * @param  QueryFormat           $format      The encoding format {@see QueryFormat}. Defaults to RFC 1738.
     * @return string The updated URL.
     *
     * @example Url::withQuery('https://example.com', ['page' => 1]); // => 'https://example.com?page=1'
     *
     * @see Url::buildQuery(), Url::build()
     */
    public static function withQuery(string $url, array $parameters, QueryFormat $format = QueryFormat::Rfc1738): string
    {
        $parts = self::parse($url);
        $query = self::buildQuery($parameters, $format);
        $parts['query'] = $query === '' ? null : $query;

        return self::build($parts);
    }

    /**
     * Sets or updates a single query parameter in a URL.
     *
     * Retrieves existing parameters, adds or updates the specified key,
     * and rebuilds the URL query component.
     *
     * @param  string       $url     The original URL string.
     * @param  string       $key     The parameter key to set.
     * @param  mixed        $value   The parameter value.
     * @param  QueryFormat  $format  The encoding format {@see QueryFormat}. Defaults to RFC 1738.
     * @return string The updated URL.
     *
     * @example Url::withQueryValue('https://example.com?a=1', 'b', 2); // => 'https://example.com?a=1&b=2'
     * @example Url::withQueryValue('https://example.com?a=1', 'a', 2); // => 'https://example.com?a=2'
     *
     * @see Url::queryParameters(), Url::withQuery()
     */
    public static function withQueryValue(string $url, string $key, mixed $value, QueryFormat $format = QueryFormat::Rfc1738): string
    {
        $parameters = self::queryParameters($url);
        $parameters[$key] = $value;

        return self::withQuery($url, $parameters, $format);
    }

    /**
     * Removes a single query parameter from a URL.
     *
     * Retrieves existing parameters, removes the specified key,
     * and rebuilds the URL query component.
     *
     * @param  string       $url     The original URL string.
     * @param  string       $key     The parameter key to remove.
     * @param  QueryFormat  $format  The encoding format {@see QueryFormat}. Defaults to RFC 1738.
     * @return string The updated URL.
     *
     * @example Url::withoutQueryValue('https://example.com?a=1&b=2', 'a'); // => 'https://example.com?b=2'
     *
     * @see Url::queryParameters(), Url::withQuery()
     */
    public static function withoutQueryValue(string $url, string $key, QueryFormat $format = QueryFormat::Rfc1738): string
    {
        $parameters = self::queryParameters($url);
        unset($parameters[$key]);

        return self::withQuery($url, $parameters, $format);
    }

    private static function queryEncoding(QueryFormat $format): int
    {
        return match ($format) {
            QueryFormat::Rfc1738 => PHP_QUERY_RFC1738,
            QueryFormat::Rfc3986 => PHP_QUERY_RFC3986,
        };
    }
}
