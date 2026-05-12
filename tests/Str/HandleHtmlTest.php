<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleHtmlTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function escapeHtmlProvider(): iterable
    {
        yield 'less and greater' => ['<b>hi</b>', '&lt;b&gt;hi&lt;/b&gt;'];
        yield 'ampersand' => ['Tom & Jerry', 'Tom &amp; Jerry'];
        yield 'double quote' => ['He said "hi"', 'He said &quot;hi&quot;'];
        yield 'single quote html5' => ["It's", 'It&apos;s'];
        yield 'no special chars' => ['hello world', 'hello world'];
        yield 'empty' => ['', ''];
        yield 'multibyte unaffected' => ['café', 'café'];
    }

    #[DataProvider('escapeHtmlProvider')]
    public function testEscapeHtml(string $input, string $expected): void
    {
        self::assertSame($expected, Str::escapeHtml($input));
    }

    public function testUnescapeHtmlRoundtripsEscape(): void
    {
        $original = '<a href="x">Tom & Jerry</a>';
        self::assertSame($original, Str::unescapeHtml(Str::escapeHtml($original)));
    }

    public function testUnescapeHtmlBare(): void
    {
        self::assertSame('hello', Str::unescapeHtml('hello'));
    }

    public function testEncodeEntitiesAccentedChars(): void
    {
        $encoded = Str::encodeEntities('café');
        self::assertStringContainsString('caf', $encoded);
        self::assertNotSame('café', $encoded); // must have been encoded
    }

    public function testEncodeDecodeEntitiesRoundtrip(): void
    {
        $original = 'café & <test>';
        self::assertSame($original, Str::decodeEntities(Str::encodeEntities($original)));
    }

    public function testDecodeEntitiesBasicHtml(): void
    {
        self::assertSame('<b>hi</b>', Str::decodeEntities('&lt;b&gt;hi&lt;/b&gt;'));
    }

    public function testTranslationTableSpecialChars(): void
    {
        $table = Str::translationTable();

        self::assertArrayHasKey('&', $table);
        self::assertSame('&amp;', $table['&']);
        self::assertSame('&lt;', $table['<']);
        self::assertSame('&gt;', $table['>']);
    }

    public function testTranslationTableEntities(): void
    {
        $table = Str::translationTable(HTML_ENTITIES);

        self::assertGreaterThan(10, count($table));
    }

    /**
     * @return iterable<string, array{0: string, 1: list<string>, 2: string}>
     */
    public static function stripTagsProvider(): iterable
    {
        yield 'strip all' => ['<b>hi</b> <i>there</i>', [], 'hi there'];
        yield 'keep b' => ['<b>hi</b> <i>there</i>', ['b'], '<b>hi</b> there'];
        yield 'keep multiple' => ['<b>hi</b> <i>there</i>', ['b', 'i'], '<b>hi</b> <i>there</i>'];
        yield 'no tags' => ['hello', [], 'hello'];
        yield 'empty' => ['', [], ''];
        yield 'nested tags' => ['<p>a<b>b</b>c</p>', ['p'], '<p>abc</p>'];
    }

    /**
     * @param list<string> $allowed
     */
    #[DataProvider('stripTagsProvider')]
    public function testStripTags(string $input, array $allowed, string $expected): void
    {
        self::assertSame($expected, Str::stripTags($input, $allowed));
    }

    public function testLinebreaksToBrXhtml(): void
    {
        self::assertSame("a<br />\nb", Str::linebreaksToBr("a\nb"));
    }

    public function testLinebreaksToBrHtml(): void
    {
        self::assertSame("a<br>\nb", Str::linebreaksToBr("a\nb", false));
    }

    public function testLinebreaksToBrMultiple(): void
    {
        self::assertSame("a<br />\nb<br />\nc", Str::linebreaksToBr("a\nb\nc"));
    }

    public function testLinebreaksToBrEmpty(): void
    {
        self::assertSame('', Str::linebreaksToBr(''));
    }
}
