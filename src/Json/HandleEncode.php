<?php

declare(strict_types=1);

namespace Phyx\Json;

use JsonException;

trait HandleEncode
{
    /**
     * Encode a value into a JSON string.
     *
     * Uses {@see json_encode()} with `JSON_THROW_ON_ERROR` enabled by default.
     *
     * @param mixed $value The value to be encoded.
     * @param int   $flags Bitmask of JSON encode options, defaults to 0. {@see \Phyx\Enums\JsonOutput}
     * @param int   $depth Maximum nesting depth, defaults to 512.
     *
     * @return string The JSON encoded string.
     *
     * @throws \JsonException If encoding fails.
     *
     * @example
     *   Json::encode(['a' => 1]);         // => '{"a":1}'
     *   Json::encode('test');             // => '"test"'
     *   Json::encode([1, 2], JSON_FORCE_OBJECT); // => '{"0":1,"1":2}'
     *
     * @see https://www.php.net/manual/en/function.json-encode.php
     * @see \Phyx\Json::decode()
     */
    public static function encode(mixed $value, int $flags = 0, int $depth = 512): string
    {
        return json_encode($value, $flags | JSON_THROW_ON_ERROR, self::jsonDepth($depth));
    }

    /**
     * Encode a value into a pretty-printed JSON string.
     *
     * Shortcut for {@see HandleEncode::encode()} with `JSON_PRETTY_PRINT` added to flags.
     *
     * @param mixed $value The value to be encoded.
     * @param int   $flags Bitmask of JSON encode options, defaults to 0. {@see \Phyx\Enums\JsonOutput}
     * @param int   $depth Maximum nesting depth, defaults to 512.
     *
     * @return string The pretty-printed JSON string.
     *
     * @throws \JsonException If encoding fails.
     *
     * @example
     *   Json::pretty(['a' => 1]); // => "{\n    \"a\": 1\n}"
     *   Json::pretty([1, 2]);      // => "[\n    1,\n    2\n]"
     *
     * @see HandleEncode::encode()
     */
    public static function pretty(mixed $value, int $flags = 0, int $depth = 512): string
    {
        return self::encode($value, $flags | JSON_PRETTY_PRINT, $depth);
    }

    /**
     * Attempt to encode a value into a JSON string, returning null on failure.
     *
     * Provides an error-safe alternative to {@see HandleEncode::encode()} by
     * catching {@see \JsonException}.
     *
     * @param mixed $value The value to be encoded.
     * @param int   $flags Bitmask of JSON encode options, defaults to 0. {@see \Phyx\Enums\JsonOutput}
     * @param int   $depth Maximum nesting depth, defaults to 512.
     *
     * @return string|null The JSON encoded string, or null if encoding failed. {@see \Phyx\Enums\JsonErrorMode}
     *
     * @example
     *   Json::tryEncode(fopen('php://memory', 'r')); // => null
     *   Json::tryEncode(['a' => 1]);                // => '{"a":1}'
     *
     * @see HandleEncode::encode()
     */
    public static function tryEncode(mixed $value, int $flags = 0, int $depth = 512): ?string
    {
        try {
            return self::encode($value, $flags, $depth);
        } catch (JsonException) {
            return null;
        }
    }
}
