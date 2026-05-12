<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Enums\UrlComponent;
use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleParseTest extends TestCase
{
    public function testParseReturnsStableStructureForAbsoluteUrl(): void
    {
        self::assertSame([
            'scheme' => 'https',
            'user' => 'alice',
            'pass' => 'secret',
            'host' => 'example.com',
            'port' => 8443,
            'path' => '/docs/index.html',
            'query' => 'a=1&empty=',
            'fragment' => 'top',
        ], Url::parse('https://alice:secret@example.com:8443/docs/index.html?a=1&empty=#top'));
    }

    public function testParseReturnsNullForAbsentComponentsAndKeepsEmptyQueryAndFragment(): void
    {
        self::assertSame([
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => '/relative/path',
            'query' => '',
            'fragment' => '',
        ], Url::parse('/relative/path?#'));
    }

    public function testParseSupportsIpv6AndNeverExposesFalse(): void
    {
        $parts = Url::parse('http://[::1]:8080/a');

        self::assertSame('http', $parts['scheme']);
        self::assertSame('[::1]', $parts['host']);
        self::assertSame(8080, $parts['port']);
        self::assertSame('/a', $parts['path']);
        self::assertSame([
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ], Url::parse('http://:80'));
        self::assertNull(Url::tryParse('http://:80'));
    }

    public function testComponentReturnsRequestedComponent(): void
    {
        $url = 'https://alice:secret@example.com:443/p?q=1#f';

        self::assertSame('https', Url::component($url, UrlComponent::Scheme));
        self::assertSame('alice', Url::component($url, UrlComponent::User));
        self::assertSame('secret', Url::component($url, UrlComponent::Pass));
        self::assertSame('example.com', Url::component($url, UrlComponent::Host));
        self::assertSame(443, Url::component($url, UrlComponent::Port));
        self::assertSame('/p', Url::component($url, UrlComponent::Path));
        self::assertSame('q=1', Url::component($url, UrlComponent::Query));
        self::assertSame('f', Url::component($url, UrlComponent::Fragment));
        self::assertNull(Url::component('/p', UrlComponent::Host));
    }
}
