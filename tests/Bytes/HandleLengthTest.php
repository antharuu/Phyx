<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bytes::class)]
final class HandleLengthTest extends TestCase
{
    public function testLengthCountsRawBytes(): void
    {
        self::assertSame(0, Bytes::length(''));
        self::assertSame(3, Bytes::length("a\0b"));
        self::assertSame(2, Bytes::length('é'));
        self::assertSame(4, Bytes::length("\x00\x01\xFE\xFF"));
    }

    public function testEmptyPredicates(): void
    {
        self::assertTrue(Bytes::isEmpty(''));
        self::assertFalse(Bytes::isEmpty("\0"));
        self::assertFalse(Bytes::isNotEmpty(''));
        self::assertTrue(Bytes::isNotEmpty("\0"));
    }
}
