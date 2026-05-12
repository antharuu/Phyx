<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use Phyx\Bytes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Bytes::class)]
final class HandleByteTest extends TestCase
{
    public function testFromIntAndToInt(): void
    {
        self::assertSame("\0", Bytes::fromInt(0));
        self::assertSame('A', Bytes::fromInt(65));
        self::assertSame("\xFF", Bytes::fromInt(255));
        self::assertSame(0, Bytes::toInt("\0"));
        self::assertSame(195, Bytes::toInt("\xC3"));
    }

    public function testFromIntRejectsValuesOutsideByteRange(): void
    {
        $this->expectException(ValueError::class);

        Bytes::fromInt(256);
    }

    public function testToIntRequiresExactlyOneByte(): void
    {
        $this->expectException(ValueError::class);

        Bytes::toInt('é');
    }

    public function testIntsAndFromInts(): void
    {
        self::assertSame([], Bytes::ints(''));
        self::assertSame([97, 0, 98], Bytes::ints("a\0b"));
        self::assertSame([195, 169], Bytes::ints('é'));
        self::assertSame("a\0b", Bytes::fromInts([97, 0, 98]));
    }

    public function testFromIntsRejectsInvalidByte(): void
    {
        $this->expectException(ValueError::class);

        Bytes::fromInts([1, -1]);
    }
}
