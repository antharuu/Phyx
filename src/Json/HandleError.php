<?php

declare(strict_types=1);

namespace Phyx\Json;

trait HandleError
{
    /**
     * Retrieve the last JSON error code.
     *
     * Returns the integer error code from the last JSON operation.
     *
     * @return int The error code constant (e.g., `JSON_ERROR_NONE`).
     *
     * @example
     *   Json::lastError(); // => 0
     *
     * @see https://www.php.net/manual/en/function.json-last-error.php
     */
    public static function lastError(): int
    {
        return json_last_error();
    }

    /**
     * Retrieve the last JSON error message.
     *
     * Returns a human-readable string describing the last JSON error.
     *
     * @return string The error message.
     *
     * @example
     *   Json::lastErrorMessage(); // => 'No error'
     *
     * @see https://www.php.net/manual/en/function.json-last-error-msg.php
     */
    public static function lastErrorMessage(): string
    {
        return json_last_error_msg();
    }
}
