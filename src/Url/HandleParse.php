<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\UrlComponent;

trait HandleParse
{
    /**
     * Parses a URL into its constituent components.
     *
     * Deconstructs a URI string into its components (scheme, user, pass, host, port, path, query, fragment)
     * according to RFC 3986. Returns a standardized array where missing components are null.
     *
     * @param  string  $url  The URL string to parse.
     * @return array{scheme:?string,user:?string,pass:?string,host:?string,port:?int,path:?string,query:?string,fragment:?string} Array containing the URL components.
     *
     * @example Url::parse('https://user:pass@example.com:8080/path?query#frag');
     *          // => ['scheme' => 'https', 'user' => 'user', 'pass' => 'pass', 'host' => 'example.com', 'port' => 8080, 'path' => '/path', 'query' => 'query', 'fragment' => 'frag']
     * @example Url::parse('https://example.com');
     *          // => ['scheme' => 'https', 'user' => null, 'pass' => null, 'host' => 'example.com', 'port' => null, 'path' => null, 'query' => null, 'fragment' => null]
     *
     * @see parse_url()
     */
    public static function parse(string $url): array
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return self::emptyParts();
        }

        return self::stableParts($parts);
    }

    /**
     * Attempts to parse a URL and returns null on failure.
     *
     * Processes the URL string and returns the components array if the URL is well-formed,
     * or null if parsing fails according to RFC 3986.
     *
     * @param  string  $url  The URL string to parse.
     * @return array{scheme:?string,user:?string,pass:?string,host:?string,port:?int,path:?string,query:?string,fragment:?string}|null Components array on success, null on failure.
     *
     * @example Url::tryParse('https://example.com');
     *          // => ['scheme' => 'https', 'host' => 'example.com', ...]
     * @example Url::tryParse('http://:80');
     *          // => null
     *
     * @see parse_url()
     */
    public static function tryParse(string $url): ?array
    {
        $parts = parse_url($url);

        return $parts === false ? null : self::stableParts($parts);
    }

    /**
     * Retrieves a specific component from a URL.
     *
     * Extracts a single part of the URI based on the provided component enum.
     * Returns the value as a string (or int for port), or null if the component is missing.
     *
     * @param  string        $url        The URL string.
     * @param  UrlComponent  $component  The component to extract {@see UrlComponent}.
     * @return string|int|null The component value or null.
     *
     * @example Url::component('https://example.com', UrlComponent::Host); // => 'example.com'
     * @example Url::component('https://example.com:8080', UrlComponent::Port); // => 8080
     *
     * @see parse_url()
     */
    public static function component(string $url, UrlComponent $component): string|int|null
    {
        $parts = self::parse($url);

        return match ($component) {
            UrlComponent::Scheme => $parts['scheme'],
            UrlComponent::User => $parts['user'],
            UrlComponent::Pass => $parts['pass'],
            UrlComponent::Host => $parts['host'],
            UrlComponent::Port => $parts['port'],
            UrlComponent::Path => $parts['path'],
            UrlComponent::Query => $parts['query'],
            UrlComponent::Fragment => $parts['fragment'],
        };
    }

    /**
     * @param array<string, mixed> $parts
     * @return array{scheme:?string,user:?string,pass:?string,host:?string,port:?int,path:?string,query:?string,fragment:?string}
     */
    private static function stableParts(array $parts): array
    {
        return [
            'scheme' => array_key_exists('scheme', $parts) ? (string) $parts['scheme'] : null,
            'user' => array_key_exists('user', $parts) ? (string) $parts['user'] : null,
            'pass' => array_key_exists('pass', $parts) ? (string) $parts['pass'] : null,
            'host' => array_key_exists('host', $parts) ? (string) $parts['host'] : null,
            'port' => array_key_exists('port', $parts) ? (int) $parts['port'] : null,
            'path' => array_key_exists('path', $parts) ? (string) $parts['path'] : null,
            'query' => array_key_exists('query', $parts) ? (string) $parts['query'] : null,
            'fragment' => array_key_exists('fragment', $parts) ? (string) $parts['fragment'] : null,
        ];
    }

    /**
     * @return array{scheme:?string,user:?string,pass:?string,host:?string,port:?int,path:?string,query:?string,fragment:?string}
     */
    private static function emptyParts(): array
    {
        return [
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];
    }
}
