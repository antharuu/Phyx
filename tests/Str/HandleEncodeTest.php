<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleEncodeTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function toHexProvider(): iterable
    {
        yield 'ascii abc' => ['abc', '616263'];
        yield 'single high byte' => ["\xff", 'ff'];
        yield 'empty' => ['', ''];
        yield 'mixed bytes' => ["\x00\x10\xff", '0010ff'];
    }

    #[DataProvider('toHexProvider')]
    public function testToHex(string $input, string $expected): void
    {
        self::assertSame($expected, Str::toHex($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function fromHexProvider(): iterable
    {
        yield 'ascii abc' => ['616263', 'abc'];
        yield 'uppercase digits' => ['FF', "\xff"];
        yield 'mixed case' => ['aB10', "\xab\x10"];
        yield 'empty' => ['', ''];
    }

    #[DataProvider('fromHexProvider')]
    public function testFromHex(string $input, string $expected): void
    {
        self::assertSame($expected, Str::fromHex($input));
    }

    public function testFromHexRoundtripsToHex(): void
    {
        $binary = "\x00\x10\xff\xa0";
        self::assertSame($binary, Str::fromHex(Str::toHex($binary)));
    }

    public function testFromHexOddLengthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::fromHex('abc');
    }

    public function testFromHexNonHexCharThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::fromHex('zz');
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function fromCharCodeProvider(): iterable
    {
        yield 'A' => [65, 'A'];
        yield 'newline' => [10, "\n"];
        yield 'accented e' => [233, 'é'];
        yield 'waving hand emoji' => [0x1F44B, '👋'];
    }

    #[DataProvider('fromCharCodeProvider')]
    public function testFromCharCode(int $code, string $expected): void
    {
        self::assertSame($expected, Str::fromCharCode($code));
    }

    public function testFromCharCodeNegativeThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::fromCharCode(-1);
    }

    public function testFromCharCodeBeyondMaxCodepointThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::fromCharCode(0x110000);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function toCharCodeProvider(): iterable
    {
        yield 'A' => ['A', 65];
        yield 'lowercase a' => ['a', 97];
        yield 'accented e' => ['é', 233];
        yield 'emoji' => ['👋', 0x1F44B];
        yield 'first char of multi-char string' => ['Hi', 72];
    }

    #[DataProvider('toCharCodeProvider')]
    public function testToCharCode(string $input, int $expected): void
    {
        self::assertSame($expected, Str::toCharCode($input));
    }

    public function testToCharCodeEmptyThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::toCharCode('');
    }

    public function testToCharCodeInvalidUtf8ByteThrows(): void
    {
        // 0x80 alone is not a valid UTF-8 lead byte; mb_ord returns false and Phyx throws.
        $this->expectException(\ValueError::class);
        Str::toCharCode("\x80");
    }
}
