<?php

declare(strict_types=1);

namespace Phyx\Json;

use JsonException;
use stdClass;

trait HandleDecode
{
    /**
     * Decode a JSON string into a PHP value.
     *
     * Uses {@see json_decode()} with `JSON_THROW_ON_ERROR` enabled by default.
     *
     * @param string $json  The JSON string to decode.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     * @param int    $flags Bitmask of JSON decode options, defaults to 0.
     *
     * @return array<mixed>|object|string|int|float|bool|null The decoded value. {@see \\Phyx\\Enums\\JsonShape}
     *
     * @throws \JsonException If decoding fails.
     *
     * @example
     *   Json::decode('{"a":1}'); // => (object) ['a' => 1]
     *   Json::decode('true');    // => true
     *
     * @see https://www.php.net/manual/en/function.json-decode.php
     * @see \Phyx\Json::encode()
     */
    public static function decode(string $json, int $depth = 512, int $flags = 0): array|object|string|int|float|bool|null
    {
        return json_decode($json, false, self::jsonDepth($depth), $flags | JSON_THROW_ON_ERROR);
    }

    /**
     * Decode a JSON string specifically into an associative array.
     *
     * Ensures the result is an array, throwing {@see \JsonException} otherwise.
     *
     * @param string $json  The JSON string to decode.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     * @param int    $flags Bitmask of JSON decode options, defaults to 0.
     *
     * @return array<mixed> The decoded associative array.
     *
     * @throws \JsonException If decoding fails or the result is not an array.
     *
     * @example
     *   Json::decodeArray('{"a":1}'); // => ['a' => 1]
     *
     * @see HandleDecode::decode()
     */
    public static function decodeArray(string $json, int $depth = 512, int $flags = 0): array
    {
        $decoded = json_decode($json, true, self::jsonDepth($depth), $flags | JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new JsonException('Decoded JSON value is not an array.');
        }

        return $decoded;
    }

    /**
     * Decode a JSON string specifically into a stdClass object.
     *
     * Ensures the result is a stdClass object, throwing {@see \JsonException} otherwise.
     *
     * @param string $json  The JSON string to decode.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     * @param int    $flags Bitmask of JSON decode options, defaults to 0.
     *
     * @return \stdClass The decoded object.
     *
     * @throws \JsonException If decoding fails or the result is not an object.
     *
     * @example
     *   Json::decodeObject('{"a":1}'); // => (object) ['a' => 1]
     *
     * @see HandleDecode::decode()
     */
    public static function decodeObject(string $json, int $depth = 512, int $flags = 0): stdClass
    {
        $decoded = json_decode($json, false, self::jsonDepth($depth), $flags | JSON_THROW_ON_ERROR);

        if (!$decoded instanceof stdClass) {
            throw new JsonException('Decoded JSON value is not an object.');
        }

        return $decoded;
    }

    /**
     * Attempt to decode a JSON string, returning null on failure.
     *
     * Provides an error-safe alternative to {@see HandleDecode::decode()} by
     * catching {@see \JsonException}.
     *
     * @param string $json  The JSON string to decode.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     * @param int    $flags Bitmask of JSON decode options, defaults to 0.
     *
     * @return array<mixed>|object|string|int|float|bool|null The decoded value, or null if decoding failed. {@see \Phyx\Enums\JsonErrorMode}
     *
     * @example
     *   Json::tryDecode('{invalid}'); // => null
     *
     * @see HandleDecode::decode()
     */
    public static function tryDecode(string $json, int $depth = 512, int $flags = 0): array|object|string|int|float|bool|null
    {
        try {
            return self::decode($json, $depth, $flags);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Attempt to decode a JSON string into an array, returning null on failure.
     *
     * Provides an error-safe alternative to {@see HandleDecode::decodeArray()} by
     * catching {@see \JsonException}.
     *
     * @param string $json  The JSON string to decode.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     * @param int    $flags Bitmask of JSON decode options, defaults to 0.
     *
     * @return array<mixed>|null The decoded array, or null if decoding failed. {@see \Phyx\Enums\JsonErrorMode}
     *
     * @example
     *   Json::tryDecodeArray('{invalid}'); // => null
     *
     * @see HandleDecode::decodeArray()
     */
    public static function tryDecodeArray(string $json, int $depth = 512, int $flags = 0): ?array
    {
        try {
            return self::decodeArray($json, $depth, $flags);
        } catch (JsonException) {
            return null;
        }
    }
}
