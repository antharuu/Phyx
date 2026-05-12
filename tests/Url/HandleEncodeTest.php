<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Enums\UrlEncoding;
use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleEncodeTest extends TestCase
{
    public function testEncodeDecodeSupportsFormAndRfc3986(): void
    {
        self::assertSame('a+b%2Fc', Url::encode('a b/c', UrlEncoding::Form));
        self::assertSame('a%20b%2Fc', Url::encode('a b/c', UrlEncoding::Rfc3986));
        self::assertSame('a b/c', Url::decode('a+b%2Fc', UrlEncoding::Form));
        self::assertSame('a+b/c', Url::decode('a+b%2Fc', UrlEncoding::Rfc3986));
    }

    public function testComponentEncodingUsesRfc3986(): void
    {
        self::assertSame('a%20b%2Fc', Url::encodeComponent('a b/c'));
        self::assertSame('a b/c', Url::decodeComponent('a%20b%2Fc'));
    }
}
