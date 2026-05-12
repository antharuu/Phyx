<?php

declare(strict_types=1);

namespace Phyx\Tests\Html;

use Phyx\Enums\Encoding;
use Phyx\Enums\HtmlQuotes;
use Phyx\Html;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Html::class)]
final class HandleEntitiesTest extends TestCase
{
    public function testEncodeEntitiesEncodesAccentedCharactersAndQuotes(): void
    {
        self::assertSame('caf&eacute; &amp; &quot;th&eacute;&quot; &apos;x&apos;', Html::encodeEntities('café & "thé" \'x\''));
    }

    public function testEncodeEntitiesCanControlQuotes(): void
    {
        self::assertSame('&quot;x&quot; \'y\'', Html::encodeEntities('"x" \'y\'', HtmlQuotes::Double));
        self::assertSame('"x" &apos;y&apos;', Html::encodeEntities('"x" \'y\'', HtmlQuotes::Single));
    }

    public function testDecodeEntitiesRoundtrips(): void
    {
        self::assertSame('café & "x"', Html::decodeEntities('caf&eacute; &amp; &quot;x&quot;'));
        self::assertSame('\' &quot;', Html::decodeEntities('&apos; &quot;', HtmlQuotes::Single));
    }

    public function testEntitiesSupportEncodingEnum(): void
    {
        self::assertSame("\xe9", Html::decodeEntities('&eacute;', HtmlQuotes::Both, Encoding::Iso88591));
    }
}
