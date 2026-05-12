<?php

declare(strict_types=1);

namespace Phyx\Polyfills;

use JsonException;
use ValueError;

/**
 * PHP 8.1/8.2-compatible implementation of PHP 8.3's json_validate().
 */
final class JsonValidate
{
    /**
     * Validate JSON without exposing the decoded value to callers.
     *
     * @throws ValueError When flags are non-zero or depth is outside PHP's
     * accepted JSON depth range.
     */
    public static function validate(string $json, int $depth = 512, int $flags = 0): bool
    {
        if ($flags !== 0) {
            throw new ValueError('json_validate(): Argument #3 ($flags) must be 0');
        }

        if ($depth < 1 || $depth > 2147483647) {
            throw new ValueError('json_validate(): Argument #2 ($depth) must be greater than 0');
        }

        try {
            json_decode($json, null, $depth, JSON_THROW_ON_ERROR);

            return true;
        } catch (JsonException) {
            return false;
        }
    }
}
