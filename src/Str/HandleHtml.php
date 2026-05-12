<?php

declare(strict_types=1);

namespace Phyx\Str;

use Phyx\Enums\Encoding;

/**
 * HTML entity and tag operations for {@see \Phyx\Str}.
 *
 * Modern defaults: every method that accepts an `$flags` argument defaults
 * to `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`, which is the only safe
 * combination for general web output.
 */
trait HandleHtml
{
    /**
     * The default flag set used by every HTML-encoding method in this trait.
     *
     * `ENT_QUOTES` makes both single and double quotes escape — necessary
     * when embedding values in attributes. `ENT_SUBSTITUTE` replaces
     * invalid sequences with the Unicode replacement character instead of
     * returning an empty string. `ENT_HTML5` follows the HTML5 entity set.
     */
    private const DEFAULT_HTML_FLAGS = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5;

    /**
     * Escape the five HTML special characters in `$value`.
     *
     * Wraps PHP's `htmlspecialchars` with safe defaults
     * (`ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`). Use this when injecting
     * arbitrary text into an HTML context, including inside attribute values.
     *
     * @param string $value The string to escape.
     * @param int $flags A bitmask of `ENT_*` flags. Defaults to
     *                            `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`.
     * @param Encoding $encoding The encoding of `$value`. Defaults to {@see Encoding::Utf8}.
     * @return string             The escaped string.
     *
     * @example
     *   Str::escapeHtml('<a href="x">Tom & Jerry</a>');
     *   // => '&lt;a href=&quot;x&quot;&gt;Tom &amp; Jerry&lt;/a&gt;'
     *
     * @see HandleHtml::unescapeHtml The inverse operation.
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php PHP native equivalent
     */
    public static function escapeHtml(
        string   $value,
        int      $flags = self::DEFAULT_HTML_FLAGS,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        return htmlspecialchars($value, $flags, $encoding->value);
    }

    /**
     * Reverse `escapeHtml` — decode the five HTML special-character entities.
     *
     * Wraps PHP's `htmlspecialchars_decode`. Only decodes `&amp;`, `&lt;`,
     * `&gt;`, `&quot;` and `&#039;` / `&apos;`; for the full entity set
     * use {@see HandleHtml::decodeEntities()}.
     *
     * @param string $value The string to decode.
     * @param int $flags A bitmask of `ENT_*` flags. Defaults to
     *                       `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`.
     * @return string        The decoded string.
     *
     * @example
     *   Str::unescapeHtml('&lt;b&gt;hi&lt;/b&gt;');  // => '<b>hi</b>'
     *
     * @see HandleHtml::escapeHtml The inverse operation.
     * @see https://www.php.net/manual/en/function.htmlspecialchars-decode.php PHP native equivalent
     */
    public static function unescapeHtml(
        string $value,
        int    $flags = self::DEFAULT_HTML_FLAGS,
    ): string
    {
        return htmlspecialchars_decode($value, $flags);
    }

    /**
     * Encode every translatable character of `$value` as its HTML entity.
     *
     * Wraps PHP's `htmlentities`. Unlike `escapeHtml`, this also encodes
     * accented letters and other named-entity characters — useful when the
     * downstream consumer cannot guarantee UTF-8.
     *
     * @param string $value The string to encode.
     * @param int $flags A bitmask of `ENT_*` flags. Defaults to
     *                            `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`.
     * @param Encoding $encoding The encoding of `$value`. Defaults to {@see Encoding::Utf8}.
     * @return string             The encoded string.
     *
     * @example
     *   Str::encodeEntities('café'); // => 'caf&eacute;'
     *
     * @see HandleHtml::decodeEntities The inverse operation.
     * @see https://www.php.net/manual/en/function.htmlentities.php PHP native equivalent
     */
    public static function encodeEntities(
        string   $value,
        int      $flags = self::DEFAULT_HTML_FLAGS,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        return htmlentities($value, $flags, $encoding->value);
    }

    /**
     * Reverse `encodeEntities` — decode all HTML entities back to characters.
     *
     * Wraps PHP's `html_entity_decode`.
     *
     * @param string $value The string to decode.
     * @param int $flags A bitmask of `ENT_*` flags. Defaults to
     *                            `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`.
     * @param Encoding $encoding The encoding of `$value`. Defaults to {@see Encoding::Utf8}.
     * @return string             The decoded string.
     *
     * @example
     *   Str::decodeEntities('caf&eacute;');     // => 'café'
     *   Str::decodeEntities('&lt;b&gt;hi&lt;/b&gt;'); // => '<b>hi</b>'
     *
     * @see HandleHtml::encodeEntities The inverse operation.
     * @see https://www.php.net/manual/en/function.html-entity-decode.php PHP native equivalent
     */
    public static function decodeEntities(
        string   $value,
        int      $flags = self::DEFAULT_HTML_FLAGS,
        Encoding $encoding = Encoding::Utf8,
    ): string
    {
        return html_entity_decode($value, $flags, $encoding->value);
    }

    /**
     * Return the translation table used by Phyx's HTML encoders.
     *
     * Wraps PHP's `get_html_translation_table`. Useful for building
     * inverse-mapping tools or for inspection.
     *
     * @param int $table Which table — `HTML_SPECIALCHARS` (default,
     *                            same set as `escapeHtml`) or `HTML_ENTITIES`
     *                            (full named-entity set, as `encodeEntities`).
     * @param int $flags A bitmask of `ENT_*` flags. Defaults to
     *                            `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5`.
     * @param Encoding $encoding The encoding for the table. Defaults to {@see Encoding::Utf8}.
     * @return array<string, string> Map of original character => entity string.
     *
     * @example
     *   Str::translationTable();  // => ['"' => '&quot;', '&' => '&amp;', …]
     *
     * @see https://www.php.net/manual/en/function.get-html-translation-table.php PHP native equivalent
     */
    public static function translationTable(
        int      $table = HTML_SPECIALCHARS,
        int      $flags = self::DEFAULT_HTML_FLAGS,
        Encoding $encoding = Encoding::Utf8,
    ): array
    {
        return get_html_translation_table($table, $flags, $encoding->value);
    }

    /**
     * Strip every HTML/PHP tag from `$value`, optionally keeping a whitelist.
     *
     * Wraps PHP's `strip_tags`. The `$allowed` parameter is a list of tag
     * names (without angle brackets) to preserve — the array form is the
     * only one Phyx accepts (the legacy string form is error-prone and was
     * deprecated in PHP 7.4 in favour of arrays).
     *
     * @param string $value The string to strip.
     * @param list<string> $allowed The tag names to preserve (e.g. `['p', 'a']`).
     *                               Defaults to none.
     * @return string                The stripped string.
     *
     * @example
     *   Str::stripTags('<b>hi</b> <i>there</i>');         // => 'hi there'
     *   Str::stripTags('<b>hi</b> <i>there</i>', ['b']);  // => '<b>hi</b> there'
     *
     * @see https://www.php.net/manual/en/function.strip-tags.php PHP native equivalent
     */
    public static function stripTags(string $value, array $allowed = []): string
    {
        return strip_tags($value, $allowed);
    }

    /**
     * Insert an HTML `<br />` before every line break in `$value`.
     *
     * Wraps PHP's `nl2br` with `$isXhtml = true` so the output is also
     * valid XHTML. The original line break is preserved after the `<br />`.
     *
     * @param string $value The string to convert.
     * @param bool $isXhtml When `true` (default), emit `<br />`; when
     *                         `false`, emit `<br>`.
     * @return string          The converted string.
     *
     * @example
     *   Str::linebreaksToBr("a\nb");           // => "a<br />\nb"
     *   Str::linebreaksToBr("a\nb", false);    // => "a<br>\nb"
     *
     * @see https://www.php.net/manual/en/function.nl2br.php PHP native equivalent
     */
    public static function linebreaksToBr(string $value, bool $isXhtml = true): string
    {
        return nl2br($value, $isXhtml);
    }
}
