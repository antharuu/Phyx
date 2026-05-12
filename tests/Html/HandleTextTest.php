<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use Phyx\Enums\HtmlDoctype;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
#[CoversClass(HtmlDoctype::class)]
final class HandleTextTest extends TestCase
{
    public function testLinebreaksToBrDefaultsToXhtml(): void
    {
        self::assertSame("a<br />\nb", Html::linebreaksToBr("a\nb"));
    }

    public function testLinebreaksToBrCanUseHtml5(): void
    {
        self::assertSame("a<br>\nb", Html::linebreaksToBr("a\nb", HtmlDoctype::Html5));
    }

    public function testTextEscapesPlainText(): void
    {
        self::assertSame('&lt;b&gt;café &amp; &quot;x&quot;&lt;/b&gt;', Html::text('<b>café & "x"</b>'));
    }

    public function testParagraphsEscapesTextAndSplitsBlankLines(): void
    {
        self::assertSame("<p>Hello &lt;b&gt;world&lt;/b&gt;</p><p>Second<br />\nline</p>", Html::paragraphs("Hello <b>world</b>\n\nSecond\nline"));
    }

    public function testParagraphsReturnsEmptyForBlankInput(): void
    {
        self::assertSame('', Html::paragraphs(" \n\t "));
    }
}
