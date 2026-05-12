<?php

declare(strict_types=1);

namespace Phyx\Html;

/**
 * HTML tag manipulation and filtering utilities for {@see \Phyx\Html}.
 *
 * This trait provides methods for stripping tags from strings and checking
 * for the presence of HTML markup. It ensures that only allowed tags are
 * preserved when filtering content.
 */
trait HandleTags
{
    /**
     * Remove HTML and PHP tags from a string.
     *
     * Strips all tags from the input except those explicitly allowed in the
     * `$allowed` list. It uses PHP's native `strip_tags` with normalized
     * allowed tag formatting.
     *
     * @param string        $value   The string to filter.
     * @param list<string>  $allowed List of tags to preserve (e.g., ['b', 'i']). Defaults to [].
     * @return string The filtered string.
     *
     * @example
     *   Html::stripTags('<b>Hello</b> <i>World</i>'); // => 'Hello World'
     *   Html::stripTags('<b>Hello</b>', ['b']); // => '<b>Hello</b>'
     *
     * @see https://www.php.net/manual/en/function.strip-tags.php PHP native equivalent
     */
    public static function stripTags(string $value, array $allowed = []): string
    {
        return strip_tags($value, self::allowedTags($allowed));
    }

    /**
     * Check if a string contains any HTML or PHP tags.
     *
     * Returns `true` if stripping tags changes the string content, indicating
     * the presence of markup.
     *
     * @param string $value The string to check.
     * @return bool True if tags are present, false otherwise.
     *
     * @example
     *   Html::hasTags('Plain text'); // => false
     *   Html::hasTags('<div>Div</div>'); // => true
     */
    public static function hasTags(string $value): bool
    {
        return $value !== strip_tags($value);
    }

    /**
     * Normalize a list of allowed tags for use with `strip_tags`.
     *
     * Formats an array of tag names into the string format expected by
     * `strip_tags` (e.g., `<b><i>`).
     *
     * @param list<string> $allowed List of tag names.
     * @return string The formatted allowed tags string.
     *
     * @example
     *   Html::allowedTags(['b', 'i']); // => '<b><i>'
     *   Html::allowedTags(['DIV', ' p ']); // => '<div><p>'
     */
    public static function allowedTags(array $allowed): string
    {
        $tags = [];

        foreach ($allowed as $tag) {
            $name = trim($tag, " \t\n\r\0\x0B<>");
            if (self::isValidHtmlName($name)) {
                $tags[] = '<' . strtolower($name) . '>';
            }
        }

        return implode('', array_values(array_unique($tags)));
    }
}
