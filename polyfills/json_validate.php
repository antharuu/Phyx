<?php

declare(strict_types=1);

if (!function_exists('json_validate')) {
    /**
     * Validate that a string contains syntactically valid JSON.
     *
     * @throws ValueError When depth or flags are outside PHP's accepted range.
     */
    function json_validate(string $json, int $depth = 512, int $flags = 0): bool // NOSONAR: native PHP polyfill must keep snake_case name.
    {
        return Phyx\Polyfills\JsonValidate::validate($json, $depth, $flags);
    }
}
