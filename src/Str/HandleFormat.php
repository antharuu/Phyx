<?php

declare(strict_types=1);

namespace Phyx\Str;

use Stringable;
use ValueError;

/**
 * Formatting operations for {@see \Phyx\Str}.
 *
 * `printf`-style template formatting and locale-free number formatting.
 * I/O-bound variants (`printf`, `fprintf`, `vprintf`) are deliberately
 * excluded — Phyx never writes to a stream; callers compose the formatted
 * string and emit it themselves.
 */
trait HandleFormat
{
    /**
     * Format a `sprintf`-style template using the variadic arguments.
     *
     * Thin wrapper over `sprintf` with the Phyx convention `(value, …)`,
     * where the "value" is the template. The variadic form is convenient
     * for inline use; {@see HandleFormat::formatArgs()} accepts an array
     * for cases where the argument list is built dynamically.
     *
     * @param string $template A `sprintf`-style template.
     * @param int|float|string|bool|null|Stringable ...$args The values to inject.
     * @return string                                      The formatted string.
     *
     * @throws ValueError When `$template` is malformed.
     *
     * @example
     *   Str::format('Hello %s, you are %d', 'world', 42);  // => 'Hello world, you are 42'
     *   Str::format('%05.2f', 3.14);                      // => '03.14'
     *
     * @see HandleFormat::formatArgs Array-form of this method.
     * @see https://www.php.net/manual/en/function.sprintf.php PHP native equivalent
     */
    public static function format(string $template, int|float|string|bool|null|Stringable ...$args): string
    {
        return sprintf($template, ...$args);
    }

    /**
     * Format a `sprintf`-style template using a list of arguments.
     *
     * Thin wrapper over `vsprintf`. Useful when the argument list is built
     * dynamically; otherwise prefer {@see HandleFormat::format()} for
     * inline calls.
     *
     * @param string $template A `sprintf`-style template.
     * @param list<int|float|string|bool|null|Stringable> $args The values to inject, in order.
     * @return string                                                    The formatted string.
     *
     * @throws ValueError When `$template` is malformed or `$args` count
     *                     does not match the placeholder count.
     *
     * @example
     *   Str::formatArgs('Hello %s, you are %d', ['world', 42]);  // => 'Hello world, you are 42'
     *
     * @see HandleFormat::format Variadic form of this method.
     * @see https://www.php.net/manual/en/function.vsprintf.php PHP native equivalent
     */
    public static function formatArgs(string $template, array $args): string
    {
        return vsprintf($template, $args);
    }

    /**
     * Format a number with grouped thousands and a fixed decimal count.
     *
     * Wraps PHP's `number_format`. Locale-independent: the decimal and
     * thousands separators are passed in, never read from the runtime
     * locale. Negative `$decimals` round to the corresponding power of ten
     * on every supported PHP version, matching PHP 8.3+ native behaviour.
     *
     * @param float $number The number to format.
     * @param int $decimals Number of decimals to show. Defaults to `0`.
     * @param string $decimalSeparator Decimal separator. Defaults to `'.'`.
     * @param string $thousandsSeparator Thousands separator. Defaults to `','`.
     * @return string                     The formatted number.
     *
     * @example
     *   Str::formatNumber(1234567.891);                  // => '1,234,568'
     *   Str::formatNumber(1234567.891, 2);               // => '1,234,567.89'
     *   Str::formatNumber(1234567.891, 2, ',', ' ');     // => '1 234 567,89'  (FR style)
     *   Str::formatNumber(1234.5, -3);                   // => '1,000'
     *
     * @see https://www.php.net/manual/en/function.number-format.php PHP native equivalent
     */
    public static function formatNumber(
        float  $number,
        int    $decimals = 0,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ',',
    ): string
    {
        if ($decimals < 0 && PHP_VERSION_ID < 80300) {
            $number = round($number, $decimals);
            $decimals = 0;
        }

        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}
