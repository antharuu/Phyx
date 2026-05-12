<?php

declare(strict_types=1);

namespace Phyx\Url;

use Phyx\Enums\UrlEncoding;

trait HandleEncode
{
    /**
     * Encodes a string for use in a URL.
     *
     * Encodes the given string using either application/x-www-form-urlencoded (RFC 1738)
     * or RFC 3986 encoding schemes.
     *
     * @param  string       $value     The string to encode.
     * @param  UrlEncoding  $encoding  The encoding scheme {@see UrlEncoding}. Defaults to Form.
     * @return string The encoded string.
     *
     * @example Url::encode('foo bar'); // => 'foo+bar'
     * @example Url::encode('foo bar', UrlEncoding::Rfc3986); // => 'foo%20bar'
     *
     * @see urlencode(), rawurlencode()
     */
    public static function encode(string $value, UrlEncoding $encoding = UrlEncoding::Form): string
    {
        return match ($encoding) {
            UrlEncoding::Form => urlencode($value),
            UrlEncoding::Rfc3986 => rawurlencode($value),
        };
    }

    /**
     * Decodes a URL-encoded string.
     *
     * Decodes the given string using either application/x-www-form-urlencoded (RFC 1738)
     * or RFC 3986 encoding schemes.
     *
     * @param  string       $value     The encoded string.
     * @param  UrlEncoding  $encoding  The encoding scheme {@see UrlEncoding}. Defaults to Form.
     * @return string The decoded string.
     *
     * @example Url::decode('foo+bar'); // => 'foo bar'
     * @example Url::decode('foo%20bar', UrlEncoding::Rfc3986); // => 'foo bar'
     *
     * @see urldecode(), rawurldecode()
     */
    public static function decode(string $value, UrlEncoding $encoding = UrlEncoding::Form): string
    {
        return match ($encoding) {
            UrlEncoding::Form => urldecode($value),
            UrlEncoding::Rfc3986 => rawurldecode($value),
        };
    }

    /**
     * Encodes a URL component according to RFC 3986.
     *
     * Encodes the given string for use as a component of a URI (e.g., path, query, fragment)
     * where spaces are encoded as '%20'.
     *
     * @param  string  $value  The component string to encode.
     * @return string The encoded component.
     *
     * @example Url::encodeComponent('foo bar'); // => 'foo%20bar'
     *
     * @see rawurlencode()
     */
    public static function encodeComponent(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * Decodes a URL component according to RFC 3986.
     *
     * Decodes the given RFC 3986 encoded component string.
     *
     * @param  string  $value  The encoded component string.
     * @return string The decoded component.
     *
     * @example Url::decodeComponent('foo%20bar'); // => 'foo bar'
     *
     * @see rawurldecode()
     */
    public static function decodeComponent(string $value): string
    {
        return rawurldecode($value);
    }
}
