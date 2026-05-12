<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Bytes::class)]
final class HandleSliceTest extends TestCase
{
    public function testSliceUsesByteOffsets(): void
    {
        self::assertSame('bc', Bytes::slice('abcde', 1, 2));
        self::assertSame('de', Bytes::slice('abcde', -2));
        self::assertSame('abc', Bytes::slice('abcde', 0, -2));
        self::assertSame('', Bytes::slice('abc', 99));
        self::assertSame("\xA9", Bytes::slice('é', 1, 1));
    }

    public function testTakeAndTakeLast(): void
    {
        self::assertSame('ab', Bytes::take('abcd', 2));
        self::assertSame('', Bytes::take('abcd', 0));
        self::assertSame('', Bytes::take('abcd', -1));
        self::assertSame('cd', Bytes::takeLast('abcd', 2));
        self::assertSame('', Bytes::takeLast('abcd', 0));
        self::assertSame('', Bytes::takeLast('abcd', -1));
        self::assertSame('abcd', Bytes::takeLast('abcd', 99));
    }

    public function testByteAtReturnsOneByteOrNull(): void
    {
        self::assertSame('a', Bytes::byteAt('abc', 0));
        self::assertSame('c', Bytes::byteAt('abc', -1));
        self::assertSame("\0", Bytes::byteAt("a\0b", 1));
        self::assertSame("\xC3", Bytes::byteAt('é', 0));
        self::assertNull(Bytes::byteAt('abc', 3));
        self::assertNull(Bytes::byteAt('', 0));
    }
}
