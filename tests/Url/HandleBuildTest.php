<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleBuildTest extends TestCase
{
    public function testBuildReconstructsUrlFromParts(): void
    {
        self::assertSame(
            'https://alice:secret@example.com:8443/docs?a=1#top',
            Url::build([
                'scheme' => 'https',
                'user' => 'alice',
                'pass' => 'secret',
                'host' => 'example.com',
                'port' => 8443,
                'path' => '/docs',
                'query' => 'a=1',
                'fragment' => 'top',
            ]),
        );
    }

    public function testBuildKeepsEmptyQueryAndFragment(): void
    {
        self::assertSame('/path?#', Url::build(['path' => '/path', 'query' => '', 'fragment' => '']));
        self::assertSame('//example.com/path', Url::build(['host' => 'example.com', 'path' => '/path']));
    }

    public function testWithMethodsMutateAndRebuild(): void
    {
        $url = 'http://example.com/old?a=1#frag';

        self::assertSame('https://example.com/old?a=1#frag', Url::withScheme($url, 'https'));
        self::assertSame('http://example.org/old?a=1#frag', Url::withHost($url, 'example.org'));
        self::assertSame('http://example.com/new?a=1#frag', Url::withPath($url, '/new'));
        self::assertSame('http://example.com/old?a=1#new', Url::withFragment($url, 'new'));
        self::assertSame('http://example.com/old?a=1#', Url::withFragment($url, ''));
        self::assertSame('http://example.com/old?a=1', Url::withoutFragment($url));
    }
}
