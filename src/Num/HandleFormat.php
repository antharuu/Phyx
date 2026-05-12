<?php

declare(strict_types=1);

namespace Phyx\Num;

use Phyx\Enums\NumberFormat;

/**
 * Display formatting helpers for {@see \Phyx\Num}.
 */
trait HandleFormat
{
    /**
     * Format a number with grouped thousands or compact representation.
     *
     * Formats the given value according to the specified decimals and separators.
     * If the mode is {@see NumberFormat::Compact}, it will use K/M/B suffixes for large numbers.
     *
     * @param int|float    $value        The number to format.
     * @param int          $decimals     The number of decimal points. Defaults to 0.
     * @param string       $decimalSep   The separator for the decimal point. Defaults to '.'.
     * @param string       $thousandsSep The separator for the thousands. Defaults to ','.
     * @param NumberFormat $mode         The formatting mode {@see NumberFormat}. Defaults to {@see NumberFormat::Default}.
     *
     * @return string The formatted number string.
     *
     * @example Num::format(1234.567, 2) // => '1,234.57'
     * @example Num::format(1500, 1, '.', ',', NumberFormat::Compact) // => '1.5K'
     *
     * @see number_format
     */
    public static function format(
        int|float $value,
        int $decimals = 0,
        string $decimalSep = '.',
        string $thousandsSep = ',',
        NumberFormat $mode = NumberFormat::Default,
    ): string {
        if ($mode === NumberFormat::Compact) {
            return self::formatCompact($value, $decimals, $decimalSep);
        }

        return number_format($value, $decimals, $decimalSep, $thousandsSep);
    }

    /**
     * Convert a number to a predictable string representation.
     *
     * Converts integers and floats to strings. Specifically handles NAN and INF / -INF
     * float values to ensure consistent output across environments.
     *
     * @param int|float $value The number to convert.
     *
     * @return string The string representation of the number.
     *
     * @example Num::toString(42)      // => '42'
     * @example Num::toString(INF)     // => 'INF'
     * @example Num::toString(acos(2)) // => 'NAN'
     */
    public static function toString(int|float $value): string
    {
        if (is_float($value)) {
            if (is_nan($value)) {
                return 'NAN';
            }

            if (is_infinite($value)) {
                return $value > 0 ? 'INF' : '-INF';
            }
        }

        return (string) $value;
    }

    /**
     * Return an ordinal representation of an integer.
     *
     * Appends an ordinal suffix (e.g., 'st', 'nd', 'rd', 'th') to the number.
     * For English ('en'), it uses internal logic. For other locales, it utilizes
     * the PHP {@see \NumberFormatter} extension.
     *
     * @param int    $value  The integer to format.
     * @param string $locale The locale for formatting. Defaults to 'en'.
     *
     * @return string The ordinal representation.
     *
     * @example Num::ordinal(1)        // => '1st'
     * @example Num::ordinal(22)       // => '22nd'
     * @example Num::ordinal(3, 'fr') // => '3e'
     */
    public static function ordinal(int $value, string $locale = 'en'): string
    {
        if (str_starts_with(strtolower($locale), 'en')) {
            $absolute = abs($value);
            $lastTwo = $absolute % 100;
            $suffix = match (true) {
                $lastTwo >= 11 && $lastTwo <= 13 => 'th',
                $absolute % 10 === 1 => 'st',
                $absolute % 10 === 2 => 'nd',
                $absolute % 10 === 3 => 'rd',
                default => 'th',
            };

            return $value . $suffix;
        }

        $formatter = new \NumberFormatter($locale, \NumberFormatter::ORDINAL);
        $formatted = $formatter->format($value);

        return $formatted === false ? (string) $value : $formatted;
    }

    /**
     * Format a number using compact notation (K, M, B).
     *
     * Divides the number by 1,000, 1,000,000, or 1,000,000,000 and appends the
     * corresponding suffix ('K', 'M', 'B').
     *
     * @param int|float $value      The number to format.
     * @param int       $decimals   The number of decimal points.
     * @param string    $decimalSep The separator for the decimal point.
     *
     * @return string The compact formatted string.
     *
     * @example Num::formatCompact(1500, 1, '.')    // => '1.5K'
     * @example Num::formatCompact(2500000, 0, '.') // => '3M'
     */
    private static function formatCompact(int|float $value, int $decimals, string $decimalSep): string
    {
        $absolute = abs($value);
        foreach ([1_000_000_000 => 'B', 1_000_000 => 'M', 1_000 => 'K'] as $threshold => $suffix) {
            if ($absolute >= $threshold) {
                return number_format($value / $threshold, $decimals, $decimalSep, '') . $suffix;
            }
        }

        return number_format($value, $decimals, $decimalSep, '');
    }
}
