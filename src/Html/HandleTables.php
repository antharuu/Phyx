<?php

declare(strict_types=1);

namespace Phyx\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlQuotes;
use Phyx\Enums\HtmlTable;

/**
 * HTML translation table utilities for {@see \Phyx\Html}.
 *
 * This trait provides access to the internal translation tables used by PHP's
 * HTML functions, allowing for custom manipulation or inspection of entity
 * mappings.
 */
trait HandleTables
{
    /**
     * Retrieve the translation table used by HTML functions.
     *
     * Returns the associative array of character-to-entity mappings for the
     * specified {@see HtmlTable} type. It respects quote handling and encoding
     * settings.
     *
     * @param HtmlTable   $table    The type of translation table. Defaults to {@see HtmlTable::SpecialChars}.
     * @param HtmlQuotes  $flags    Quote handling strategy. Defaults to {@see HtmlQuotes::Both}.
     * @param Encoding    $encoding Character encoding. Defaults to {@see Encoding::Utf8}.
     * @return array<string, string> The translation table as an associative array.
     *
     * @example
     *   Html::translationTable(HtmlTable::SpecialChars); // => ['&' => '&amp;', '"' => '&quot;', ...]
     *   Html::translationTable(HtmlTable::HTML5, HtmlQuotes::Single); // => ["'" => '&apos;', ...]
     *
     * @see https://www.php.net/manual/en/function.get-html-translation-table.php PHP native equivalent
     */
    public static function translationTable(
        HtmlTable $table = HtmlTable::SpecialChars,
        HtmlQuotes $flags = HtmlQuotes::Both,
        Encoding $encoding = Encoding::Utf8,
    ): array {
        $translationTable = get_html_translation_table($table->native(), self::quoteFlags($flags), $encoding->value);

        if ($flags === HtmlQuotes::Single) {
            $translationTable["'"] = '&apos;';
        }

        return $translationTable;
    }
}
