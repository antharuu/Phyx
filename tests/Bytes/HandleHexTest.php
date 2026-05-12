<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use InvalidArgumentException;
use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bytes::class)]
final class HandleHexTest extends TestCase
{
    public function testToHexEncodesRawBytes(): void
    {
        self::assertSame('', Bytes::toHex(''));
        self::assertSame('610062', Bytes::toHex("a\0b"));
        self::assertSame('c3a9', Bytes::toHex('é'));
        self::assertSame('0001feff', Bytes::toHex("\x00\x01\xFE\xFF"));
    }

    public function testFromHexDecodesStrictly(): void
    {
        self::assertSame('', Bytes::fromHex(''));
        self::assertSame("a\0b", Bytes::fromHex('610062'));
        self::assertSame('é', Bytes::fromHex('c3a9'));
        self::assertSame("\xFE\xFF", Bytes::fromHex('FEFF'));
    }

    public function testInvalidHexStrictThrowsAndTryReturnsNull(): void
    {
        self::assertNull(Bytes::tryFromHex('abc'));
        self::assertNull(Bytes::tryFromHex('zz'));

        $this->expectException(InvalidArgumentException::class);
        Bytes::fromHex('abc');
    }

    public function testIsHexRequiresEvenHexDigits(): void
    {
        self::assertTrue(Bytes::isHex(''));
        self::assertTrue(Bytes::isHex('00ffAA'));
        self::assertFalse(Bytes::isHex('0'));
        self::assertFalse(Bytes::isHex('00 gg'));
    }
}
