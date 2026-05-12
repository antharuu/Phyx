<?php

declare(strict_types=1);

namespace Phyx\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlDoctype;

/**
 * Text-to-HTML conversion utilities for {@see \Phyx\Html}.
 *
 * This trait provides methods for converting plain text to HTML fragments,
 * handling line breaks, paragraphs, and general text escaping. It ensures
 * that user-provided text is safe for display in an HTML context.
 */
trait HandleText
{
    /**
     * Convert line breaks to `<br>` tags using the selected doctype style.
     *
     * Uses PHP's native `nl2br` to insert HTML line breaks before all newlines
     * in a string. The style of the break tag (XHTML-compliant `<br />` vs
     * HTML5 `<br>`) is determined by the {@see HtmlDoctype} parameter.
     *
     * @param string       $value   Plain text containing line breaks.
     * @param HtmlDoctype  $doctype Doctype style. Defaults to {@see HtmlDoctype::Xhtml}.
     * @return string The text with line breaks converted to HTML breaks.
     *
     * @example
     *   Html::linebreaksToBr("a\nb"); // => "a<br />\nb"
     *   Html::linebreaksToBr("a\nb", HtmlDoctype::Html5); // => "a<br>\nb"
     *
     * @see https://www.php.net/manual/en/function.nl2br.php PHP native equivalent
     */
    public static function linebreaksToBr(string $value, HtmlDoctype $doctype = HtmlDoctype::Xhtml): string
    {
        return nl2br($value, $doctype->isXhtml());
    }

    /**
     * Wrap plain text paragraphs in `<p>` tags after escaping their content.
     *
     * Paragraphs are separated by one or more blank lines. Single line breaks
     * inside a paragraph are preserved as `<br />` tags using {@see self::linebreaksToBr()}.
     * All content is escaped using {@see self::text()} before being wrapped.
     *
     * @param string $value Plain text to convert.
     * @return string The escaped and paragraph-wrapped HTML.
     *
     * @example
     *   Html::paragraphs("Tom & Jerry\n\nOK"); // => '<p>Tom &amp; Jerry</p><p>OK</p>'
     *   Html::paragraphs(""); // => ''
     */
    public static function paragraphs(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $parts = preg_split('/(?:\R\s*){2,}/u', $trimmed) ?: [];

        $paragraphs = array_map(
            static fn (string $paragraph): string => '<p>' . self::linebreaksToBr(self::text(trim($paragraph))) . '</p>',
            $parts,
        );

        return implode('', $paragraphs);
    }

    /**
     * Escape plain text for safe insertion in HTML text context.
     *
     * This is a semantic alias for {@see HandleEscape::escape()} that defaults
     * to UTF-8 encoding. It ensures characters like `<` and `&` are converted
     * to entities to prevent XSS.
     *
     * @param string    $value    The plain text to escape.
     * @param Encoding  $encoding Character encoding. Defaults to {@see Encoding::Utf8}.
     * @return string The HTML-escaped text.
     *
     * @example
     *   Html::text('<b>Tom & Jerry</b>'); // => '&lt;b&gt;Tom &amp; Jerry&lt;/b&gt;'
     *   Html::text("It's a trap!"); // => 'It&#039;s a trap!'
     *
     * @see \Phyx\Html\HandleEscape::escape()
     */
    public static function text(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        return self::escape($value, encoding: $encoding);
    }
}
