<?php

declare(strict_types=1);

namespace Phyx\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlQuotes;

/**
 * HTML entity encoding and decoding utilities for {@see \Phyx\Html}.
 *
 * This trait provides methods for converting all applicable characters to their
 * corresponding HTML entities, beyond just the basic special characters handled
 * by escaping. It uses HTML5-compliant translation tables.
 */
trait HandleEntities
{
    /**
     * Convert all applicable characters to HTML entities.
     *
     * Uses `htmlentities` to translate characters to entities. This is more
     * extensive than {@see self::escape()} as it covers all characters that
     * have HTML entity equivalents in the selected encoding.
     *
     * @param string      $value    The plain text to encode.
     * @param HtmlQuotes  $flags    Quote handling strategy. Defaults to {@see HtmlQuotes::Both}.
     * @param Encoding    $encoding Character encoding of the input. Defaults to {@see Encoding::Utf8}.
     * @return string The entity-encoded string.
     *
     * @example
     *   Html::encodeEntities('©'); // => '&copy;'
     *   Html::encodeEntities('<b>"Tom"</b>'); // => '&lt;b&gt;&quot;Tom&quot;&lt;/b&gt;'
     *
     * @see https://www.php.net/manual/en/function.htmlentities.php PHP native equivalent
     */
    public static function encodeEntities(
        string $value,
        HtmlQuotes $flags = HtmlQuotes::Both,
        Encoding $encoding = Encoding::Utf8,
    ): string {
        if ($flags === HtmlQuotes::Single) {
            return str_replace("'", '&apos;', htmlentities($value, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, $encoding->value, false));
        }

        return htmlentities($value, self::quoteFlags($flags), $encoding->value, false);
    }

    /**
     * Convert HTML entities back to their corresponding characters.
     *
     * Reverses the transformation performed by {@see self::encodeEntities()}
     * using `html_entity_decode`. It correctly handles HTML5 entities and
     * respects the specified encoding.
     *
     * @param string      $value    The entity-encoded string to decode.
     * @param HtmlQuotes  $flags    Quote handling strategy. Defaults to {@see HtmlQuotes::Both}.
     * @param Encoding    $encoding Character encoding of the output. Defaults to {@see Encoding::Utf8}.
     * @return string The decoded plain text.
     *
     * @example
     *   Html::decodeEntities('&copy;'); // => '©'
     *   Html::decodeEntities('&lt;b&gt;'); // => '<b>'
     *
     * @see https://www.php.net/manual/en/function.html-entity-decode.php PHP native equivalent
     */
    public static function decodeEntities(
        string $value,
        HtmlQuotes $flags = HtmlQuotes::Both,
        Encoding $encoding = Encoding::Utf8,
    ): string {
        if ($flags === HtmlQuotes::Single) {
            return str_replace('&apos;', "'", html_entity_decode($value, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, $encoding->value));
        }

        return html_entity_decode($value, self::quoteFlags($flags), $encoding->value);
    }
}
