<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlQuotes;
use Phyx\Enums\HtmlTable;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
#[CoversClass(HtmlTable::class)]
final class HandleTablesTest extends TestCase
{
    public function testTranslationTableDefaultsToSpecialChars(): void
    {
        $table = Html::translationTable();

        self::assertSame('&amp;', $table['&']);
        self::assertSame('&lt;', $table['<']);
        self::assertSame('&gt;', $table['>']);
        self::assertSame('&quot;', $table['"']);
        self::assertSame('&apos;', $table["'"]);
    }

    public function testTranslationTableCanReturnEntities(): void
    {
        $table = Html::translationTable(HtmlTable::Entities, HtmlQuotes::Both, Encoding::Utf8);

        self::assertArrayHasKey('é', $table);
        self::assertSame('&eacute;', $table['é']);
    }

    public function testTranslationTableCanReturnSpecialCharsWithSingleQuotes(): void
    {
        $table = Html::translationTable(HtmlTable::SpecialChars, HtmlQuotes::Single, Encoding::Utf8);

        self::assertSame('&apos;', $table["'"]);
    }
}
