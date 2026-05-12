<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandlePartsTest extends TestCase
{
    public function testComponentAccessorsReturnTypedValues(): void
    {
        $url = 'ftp://bob:pwd@[2001:db8::1]:2121/files?a=1#frag';

        self::assertSame('ftp', Url::scheme($url));
        self::assertSame('bob', Url::user($url));
        self::assertSame('pwd', Url::password($url));
        self::assertSame('[2001:db8::1]', Url::host($url));
        self::assertSame(2121, Url::port($url));
        self::assertSame('/files', Url::path($url));
        self::assertSame('a=1', Url::query($url));
        self::assertSame('frag', Url::fragment($url));
    }

    public function testAbsentComponentsReturnNull(): void
    {
        self::assertNull(Url::scheme('/only/path'));
        self::assertNull(Url::user('/only/path'));
        self::assertNull(Url::password('/only/path'));
        self::assertNull(Url::host('/only/path'));
        self::assertNull(Url::port('/only/path'));
        self::assertSame('/only/path', Url::path('/only/path'));
        self::assertNull(Url::query('/only/path'));
        self::assertNull(Url::fragment('/only/path'));
    }
}
