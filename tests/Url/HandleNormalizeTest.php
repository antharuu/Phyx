<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleNormalizeTest extends TestCase
{
    public function testNormalizeLowercasesSchemeHostRemovesDefaultPortAndResolvesDotSegments(): void
    {
        self::assertSame(
            'https://example.com/a/c?b=2&a=1#Frag',
            Url::normalize('HTTPS://Example.COM:443/a/./b/../c?b=2&a=1#Frag'),
        );
        self::assertSame('../a/', Url::normalize('../a/./b/../'));
        self::assertSame('/', Url::normalize('/a/../../'));
    }

    public function testRemoveDefaultPortOnlyRemovesHttpAndHttpsDefaults(): void
    {
        self::assertSame('http://example.com/a', Url::removeDefaultPort('http://example.com:80/a'));
        self::assertSame('https://example.com/a', Url::removeDefaultPort('https://example.com:443/a'));
        self::assertSame('http://example.com:8080/a', Url::removeDefaultPort('http://example.com:8080/a'));
    }

    public function testSortQuerySortsParametersByKey(): void
    {
        self::assertSame('https://example.com/path?a=1&b=2&c=3', Url::sortQuery('https://example.com/path?c=3&a=1&b=2'));
    }
}
