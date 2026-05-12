<?php

declare(strict_types=1);

namespace Phyx\Json;

use JsonException;

/**
 * Validation helpers for {@see \Phyx\Json}.
 *
 * The public API targets PHP 8.3's `json_validate()` semantics while the
 * package polyfill keeps the same behaviour available on PHP 8.1 and 8.2.
 */
trait HandleValidate
{
    /**
     * Determine if a string is syntactically valid JSON.
     *
     * Validates the input string against JSON syntax rules. Invalid JSON or
     * depth values lower than 1 return `false` for a predicate-friendly API.
     *
     * @param string $json  The JSON document to validate.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     *
     * @return bool True if valid JSON, false otherwise.
     *
     * @example
     *   Json::isValid('{"ok":true}'); // => true
     *   Json::isValid('{');            // => false
     *
     * @see https://www.php.net/manual/en/function.json-validate.php
     * @see HandleValidate::assertValid()
     */
    public static function isValid(string $json, int $depth = 512): bool
    {
        if ($depth < 1) {
            return false;
        }

        return json_validate($json, $depth);
    }

    /**
     * Assert that a string is syntactically valid JSON.
     *
     * Throwing counterpart to {@see HandleValidate::isValid()}. Validates the
     * input and throws {@see \JsonException} on failure or invalid depth.
     *
     * @param string $json  The JSON document to validate.
     * @param int    $depth Maximum nesting depth, defaults to 512.
     *
     * @return void
     *
     * @throws \JsonException If the JSON is invalid or depth is less than 1.
     *
     * @example
     *   Json::assertValid('{"ok":true}'); // (no return)
     *
     * @see HandleValidate::isValid()
     */
    public static function assertValid(string $json, int $depth = 512): void
    {
        $depth = self::jsonDepth($depth);

        if (!self::isValid($json, $depth)) {
            json_decode($json, null, $depth, JSON_THROW_ON_ERROR);
        }
    }

    /**
     * Normalize and validate the JSON nesting depth.
     *
     * Ensures the depth is at least 1, throwing {@see \JsonException} if it is not.
     *
     * @param int $depth The depth to validate.
     *
     * @return int<1, max> The validated depth.
     *
     * @throws \JsonException If the depth is less than 1.
     */
    private static function jsonDepth(int $depth): int
    {
        if ($depth < 1) {
            throw new JsonException('JSON depth must be greater than 0.');
        }

        return $depth;
    }
}
