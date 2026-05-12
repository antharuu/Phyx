<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlContext;
use Phyx\Enums\HtmlQuotes;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
final class HandleEscapeTest extends TestCase
{
    public function testEscapeIsSafeByDefault(): void
    {
        self::assertSame('&lt;a href=&quot;x&quot;&gt;Tom &amp; Jerry&apos;s&lt;/a&gt;', Html::escape('<a href="x">Tom & Jerry\'s</a>'));
    }

    public function testEscapeCanControlQuotes(): void
    {
        self::assertSame('&quot;double&quot; and \'single\'', Html::escape('"double" and \'single\'', HtmlQuotes::Double));
        self::assertSame('"double" and &apos;single&apos;', Html::escape('"double" and \'single\'', HtmlQuotes::Single));
        self::assertSame('"double" and \'single\'', Html::escape('"double" and \'single\'', HtmlQuotes::None));
    }

    public function testEscapePreservesExistingEntities(): void
    {
        self::assertSame('&copy; &amp; &lt;', Html::escape('&copy; & <'));
    }

    public function testEscapeSupportsEncodingEnum(): void
    {
        self::assertSame("caf\xe9", Html::escape("caf\xe9", HtmlQuotes::Both, Encoding::Iso88591));
    }

    public function testUnescapeReversesSpecialCharacters(): void
    {
        self::assertSame('<b>Tom & Jerry\'s</b>', Html::unescape('&lt;b&gt;Tom &amp; Jerry&apos;s&lt;/b&gt;'));
        self::assertSame("Tom ' Jerry", Html::unescape('Tom &apos; Jerry', HtmlQuotes::Single));
    }

    public function testEscapeAttributeAlwaysEscapesBothQuotes(): void
    {
        self::assertSame('Tom &amp; &quot;Jerry&quot; &apos;x&apos;', Html::escapeAttribute('Tom & "Jerry" \'x\''));
    }

    public function testEscapeForContext(): void
    {
        self::assertSame('&lt;b&gt;x&lt;/b&gt;', Html::escapeFor('<b>x</b>', HtmlContext::Text));
        self::assertSame('&quot;x&quot; &amp; &apos;y&apos;', Html::escapeFor('"x" & \'y\'', HtmlContext::Attribute));
        self::assertSame('a%20b%26c', Html::escapeFor('a b&c', HtmlContext::Url));
        self::assertSame('<b>x</b>', Html::escapeFor('<b>x</b>', HtmlContext::Raw));
    }
}
