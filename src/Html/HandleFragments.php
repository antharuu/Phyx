<?php

declare(strict_types=1);

namespace Phyx\Html;

use Stringable;

/**
 * HTML fragment generation utilities for {@see \Phyx\Html}.
 *
 * This trait provides methods for programmatically creating HTML tags,
 * comments, and other markup fragments. It ensures that content and
 * attributes are properly escaped and names are validated.
 */
trait HandleFragments
{
    /**
     * Create a complete HTML tag with content and attributes.
     *
     * Generates a pair of opening and closing tags. The tag name is validated,
     * attributes are rendered and escaped, and the content is escaped using
     * {@see self::text()}.
     *
     * @param string                                               $name       The HTML tag name (e.g., 'div').
     * @param string                                               $content    The inner content of the tag.
     * @param array<string, bool|float|int|string|Stringable|null> $attributes Associative array of attributes. Defaults to [].
     * @return string The rendered HTML tag.
     *
     * @example
     *   Html::tag('div', 'Hello', ['class' => 'greet']); // => '<div class="greet">Hello</div>'
     *   Html::tag('span', '<b>Bold</b>'); // => '<span>&lt;b&gt;Bold&lt;/b&gt;</span>'
     *
     * @see self::voidTag()
     * @see \Phyx\Html\HandleText::text()
     */
    public static function tag(string $name, string $content, array $attributes = []): string
    {
        self::assertHtmlName($name);

        return '<' . $name . self::attributesSuffix($attributes) . '>' . self::text($content) . '</' . $name . '>';
    }

    /**
     * Create a void HTML tag with attributes.
     *
     * Generates a single opening tag without a closing tag, as used for elements
     * like `<img>`, `<br>`, or `<input>`. The tag name is validated and
     * attributes are rendered and escaped.
     *
     * @param string                                               $name       The HTML tag name (e.g., 'img').
     * @param array<string, bool|float|int|string|Stringable|null> $attributes Associative array of attributes. Defaults to [].
     * @return string The rendered void HTML tag.
     *
     * @example
     *   Html::voidTag('br'); // => '<br>'
     *   Html::voidTag('img', ['src' => 'logo.png', 'alt' => 'Logo']); // => '<img src="logo.png" alt="Logo">'
     */
    public static function voidTag(string $name, array $attributes = []): string
    {
        self::assertHtmlName($name);

        return '<' . $name . self::attributesSuffix($attributes) . '>';
    }

    /**
     * Create an HTML comment.
     *
     * Wraps the given value in `<!-- -->` delimiters. It ensures the comment
     * doesn't break the HTML parser by replacing any internal `-->` with `- - >`.
     *
     * @param string $value The comment content.
     * @return string The rendered HTML comment.
     *
     * @example
     *   Html::comment('This is a comment'); // => '<!-- This is a comment -->'
     *   Html::comment('Hidden --> visible'); // => '<!-- Hidden - - > visible -->'
     */
    public static function comment(string $value): string
    {
        return '<!-- ' . str_replace('-->', '- - >', $value) . ' -->';
    }

    /**
     * Render attributes with a leading space if not empty.
     *
     * Internal helper to handle the spacing between a tag name and its attributes.
     *
     * @param array<string, bool|float|int|string|Stringable|null> $attributes Associative array of attributes.
     * @return string The rendered attributes with a leading space, or empty.
     */
    private static function attributesSuffix(array $attributes): string
    {
        $rendered = self::attributes($attributes);

        return $rendered === '' ? '' : ' ' . $rendered;
    }
}
