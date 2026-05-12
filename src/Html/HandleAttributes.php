<?php

declare(strict_types=1);

namespace Phyx\Html;

use InvalidArgumentException;
use Stringable;

/**
 * HTML attribute rendering utilities for {@see \Phyx\Html}.
 *
 * This trait provides methods for building valid HTML attribute strings from
 * associative arrays. It handles name validation and ensures values are properly
 * escaped for use in HTML tags.
 */
trait HandleAttributes
{
    /**
     * Render an array of attributes into a single space-separated string.
     *
     * Iterates through the given associative array and renders each key-value
     * pair using {@see self::attribute()}. Boolean `true` values are rendered
     * as boolean attributes (e.g., `required`), while `false` and `null` values
     * are omitted.
     *
     * @param array<string, bool|float|int|string|Stringable|null> $attributes Associative array of attributes.
     * @return string The rendered attribute string, or an empty string if no attributes are present.
     *
     * @example
     *   Html::attributes(['id' => 'foo', 'class' => 'bar']); // => 'id="foo" class="bar"'
     *   Html::attributes(['required' => true, 'disabled' => false]); // => 'required'
     */
    public static function attributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $name => $value) {
            $attribute = self::attribute((string) $name, $value);
            if ($attribute !== '') {
                $parts[] = $attribute;
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Render a single HTML attribute key-value pair.
     *
     * Validates the attribute name and escapes the value. If the value is `true`,
     * it renders a boolean attribute. If the value is `false` or `null`, it
     * returns an empty string.
     *
     * @param string                              $name  The attribute name.
     * @param bool|float|int|string|Stringable|null $value The attribute value.
     * @return string The rendered attribute pair, or an empty string.
     *
     * @example
     *   Html::attribute('class', 'btn'); // => 'class="btn"'
     *   Html::attribute('checked', true); // => 'checked'
     *
     * @see self::booleanAttribute()
     * @see \Phyx\Html\HandleEscape::escapeAttribute()
     */
    public static function attribute(string $name, bool|float|int|string|Stringable|null $value): string
    {
        self::assertHtmlName($name);

        if ($value === null || $value === false) {
            return '';
        }

        if ($value === true) {
            return self::booleanAttribute($name, true);
        }

        return $name . '="' . self::escapeAttribute((string) $value) . '"';
    }

    /**
     * Render a boolean HTML attribute.
     *
     * Renders just the attribute name if `$enabled` is `true`, otherwise returns
     * an empty string. This is used for attributes like `required`, `readonly`, etc.
     *
     * @param string $name    The attribute name.
     * @param bool   $enabled Whether the attribute should be present.
     * @return string The rendered boolean attribute or an empty string.
     *
     * @example
     *   Html::booleanAttribute('required', true); // => 'required'
     *   Html::booleanAttribute('disabled', false); // => ''
     */
    public static function booleanAttribute(string $name, bool $enabled): string
    {
        self::assertHtmlName($name);

        return $enabled ? $name : '';
    }

    private static function assertHtmlName(string $name): void
    {
        if (! self::isValidHtmlName($name)) {
            throw new InvalidArgumentException("Invalid HTML name '{$name}'.");
        }
    }

    private static function isValidHtmlName(string $name): bool
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9:_-]*$/', $name) === 1;
    }
}
