<?php

declare(strict_types=1);

namespace Phyx\Tests\Bytes;

use InvalidArgumentException;
use Phyx\Bytes;
use Phyx\Enums\PaddingMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Bytes::class)]
final class HandleBase64Test extends TestCase
{
    public function testBase64RoundTrip(): void
    {
        self::assertSame('', Bytes::toBase64(''));
        self::assertSame('YQBi', Bytes::toBase64("a\0b"));
        self::assertSame('w6k=', Bytes::toBase64('é'));
        self::assertSame("\x00\x01\xFE\xFF", Bytes::fromBase64('AAH+/w=='));
    }

    public function testBase64StrictAndLenientPadding(): void
    {
        self::assertSame('f', Bytes::fromBase64('Zg=='));
        self::assertNull(Bytes::tryFromBase64('Zg'));
        self::assertSame('f', Bytes::fromBase64('Zg', PaddingMode::Lenient));
        self::assertSame('f', Bytes::tryFromBase64(" Zg\n", PaddingMode::Lenient));
    }

    public function testInvalidBase64StrictThrowsAndTryReturnsNull(): void
    {
        self::assertNull(Bytes::tryFromBase64('@@@'));

        $this->expectException(InvalidArgumentException::class);
        Bytes::fromBase64('@@@');
    }

    public function testBase64UrlRoundTripWithoutPadding(): void
    {
        $bytes = "\x00\x01\xFE\xFF";

        self::assertSame('AAH-_w', Bytes::toBase64Url($bytes));
        self::assertSame($bytes, Bytes::fromBase64Url('AAH-_w'));
        self::assertSame('f', Bytes::fromBase64Url('Zg'));
    }

    public function testInvalidBase64UrlThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Bytes::fromBase64Url('not valid!');
    }

    public function testEmptyAndMalformedLenientBase64(): void
    {
        self::assertSame('', Bytes::tryFromBase64(''));
        self::assertNull(Bytes::tryFromBase64('@@@', PaddingMode::Lenient));
        self::assertNull(Bytes::tryFromBase64('Z', PaddingMode::Lenient));
    }

    public function testBase64UrlRejectsImpossibleRemainder(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Bytes::fromBase64Url('Z');
    }
}
