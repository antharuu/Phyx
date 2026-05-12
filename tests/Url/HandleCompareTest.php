<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleCompareTest extends TestCase
{
    public function testSameOriginComparesSchemeHostAndEffectivePort(): void
    {
        self::assertTrue(Url::sameOrigin('https://example.com/a', 'https://EXAMPLE.com:443/b'));
        self::assertTrue(Url::sameOrigin('http://example.com/a', 'http://example.com:80/b'));
        self::assertTrue(Url::sameOrigin('ftp://example.com/a', 'ftp://EXAMPLE.com/b'));
        self::assertFalse(Url::sameOrigin('http://example.com', 'https://example.com'));
        self::assertFalse(Url::sameOrigin('https://example.com:8443', 'https://example.com'));
    }

    public function testSameHostComparesHostCaseInsensitively(): void
    {
        self::assertTrue(Url::sameHost('https://Example.com/a', 'ftp://example.COM/b'));
        self::assertFalse(Url::sameHost('https://example.com', 'https://other.test'));
    }

    public function testSamePathComparesNormalizedPaths(): void
    {
        self::assertTrue(Url::samePath('https://example.com/a/./b', 'http://other.test/a/b'));
        self::assertFalse(Url::samePath('https://example.com/a/b', 'https://example.com/a/c'));
    }
}
