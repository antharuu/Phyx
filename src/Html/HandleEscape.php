<?php

declare(strict_types=1);

namespace Phyx\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlContext;
use Phyx\Enums\HtmlQuotes;

/**
 * Core escaping and unescaping utilities for {@see \Phyx\Html}.
 *
 * This trait provides low-level character escaping and decoding using HTML5-compliant
 * strategies. It handles various quote styles and character encodings to prevent
 * Cross-Site Scripting (XSS) vulnerabilities in different HTML contexts.
 */
trait HandleEscape
{
    /**
     * Escape special characters for safe inclusion in HTML markup.
     *
     * Converts characters with special meaning in HTML to their corresponding
     * entities using `htmlspecialchars`. It defaults to escaping both double
     * and single quotes for maximum safety in most contexts.
     *
     * @param string      $value    The plain text to escape.
     * @param HtmlQuotes  $flags    Quote handling strategy. Defaults to {@see HtmlQuotes::Both}.
     * @param Encoding    $encoding Character encoding of the input. Defaults to {@see Encoding::Utf8}.
     * @return string The HTML-escaped string.
     *
     * @example
     *   Html::escape('Tom & "Jerry"'); // => 'Tom &amp; &quot;Jerry&quot;'
     *   Html::escape("It's ok", HtmlQuotes::None); // => 'It\'s ok'
     *
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php PHP native equivalent
     */
    public static function escape(
        string $value,
        HtmlQuotes $flags = HtmlQuotes::Both,
        Encoding $encoding = Encoding::Utf8,
    ): string {
        if ($flags === HtmlQuotes::Single) {
            return str_replace("'", '&apos;', htmlspecialchars($value, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, $encoding->value, false));
        }

        return htmlspecialchars($value, self::quoteFlags($flags), $encoding->value, false);
    }

    /**
     * Decode HTML entities back to their original characters.
     *
     * Reverses the transformation performed by {@see self::escape()} using
     * `htmlspecialchars_decode`. Ensure that the quote strategy matches
     * the one used during escaping to recover the original string correctly.
     *
     * @param string      $value The HTML-encoded string to decode.
     * @param HtmlQuotes  $flags Quote handling strategy. Defaults to {@see HtmlQuotes::Both}.
     * @return string The decoded plain text.
     *
     * @example
     *   Html::unescape('Tom &amp; &quot;Jerry&quot;'); // => 'Tom & "Jerry"'
     *   Html::unescape("It&apos;s ok", HtmlQuotes::Single); // => "It's ok"
     *
     * @see https://www.php.net/manual/en/function.htmlspecialchars-decode.php PHP native equivalent
     */
    public static function unescape(string $value, HtmlQuotes $flags = HtmlQuotes::Both): string
    {
        if ($flags === HtmlQuotes::Single) {
            return str_replace('&apos;', "'", $value);
        }

        return htmlspecialchars_decode($value, self::quoteFlags($flags));
    }

    /**
     * Escape a string specifically for use in HTML attribute values.
     *
     * Ensures the value is safe to be enclosed in double quotes within an HTML
     * tag. It forces {@see HtmlQuotes::Both} to guarantee safety regardless of
     * the attribute delimiter used by the caller.
     *
     * @param string      $value    The attribute value to escape.
     * @param Encoding    $encoding Character encoding of the input. Defaults to {@see Encoding::Utf8}.
     * @return string The escaped attribute value.
     *
     * @example
     *   Html::escapeAttribute('data-value="123"'); // => 'data-value=&quot;123&quot;'
     */
    public static function escapeAttribute(string $value, Encoding $encoding = Encoding::Utf8): string
    {
        return self::escape($value, HtmlQuotes::Both, $encoding);
    }

    /**
     * Escape a string for a specific HTML context.
     *
     * Dispatches the escaping logic based on the target {@see HtmlContext}.
     * This provides a unified interface for escaping text, attributes, or URLs.
     *
     * @param string       $value    The plain text to escape.
     * @param HtmlContext  $context  The target HTML context.
     * @param Encoding     $encoding Character encoding of the input. Defaults to {@see Encoding::Utf8}.
     * @return string The escaped value suitable for the target context.
     *
     * @example
     *   Html::escapeFor('<a>', HtmlContext::Text); // => '&lt;a&gt;'
     *   Html::escapeFor('search query', HtmlContext::Url); // => 'search%20query'
     *
     * @see HtmlContext
     */
    public static function escapeFor(
        string $value,
        HtmlContext $context,
        Encoding $encoding = Encoding::Utf8,
    ): string {
        return match ($context) {
            HtmlContext::Text => self::escape($value, HtmlQuotes::Both, $encoding),
            HtmlContext::Attribute => self::escapeAttribute($value, $encoding),
            HtmlContext::Url => rawurlencode($value),
            HtmlContext::Raw => $value,
        };
    }

    private static function quoteFlags(HtmlQuotes $quotes): int
    {
        return match ($quotes) {
            HtmlQuotes::Double => ENT_COMPAT | ENT_SUBSTITUTE | ENT_HTML5,
            HtmlQuotes::Single, HtmlQuotes::Both => ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
            HtmlQuotes::None => ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5,
        };
    }
}
