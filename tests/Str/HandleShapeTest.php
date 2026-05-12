<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\Side;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleShapeTest extends TestCase
{
    // ─────────────────────────────────────────────────────────────────────
    // reverse
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function reverseProvider(): iterable
    {
        yield 'ascii' => ['Hello', 'olleH'];
        yield 'multibyte' => ['café', 'éfac'];
        yield 'palindrome' => ['radar', 'radar'];
        yield 'empty' => ['', ''];
        yield 'single char' => ['a', 'a'];
        yield 'with whitespace' => ['a b c', 'c b a'];
    }

    #[DataProvider('reverseProvider')]
    public function testReverse(string $input, string $expected): void
    {
        self::assertSame($expected, Str::reverse($input));
    }

    // ─────────────────────────────────────────────────────────────────────
    // repeat
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, int, string}>
     */
    public static function repeatProvider(): iterable
    {
        yield 'three times' => ['ab', 3, 'ababab'];
        yield 'zero times' => ['x', 0, ''];
        yield 'one time' => ['hello', 1, 'hello'];
        yield 'empty source' => ['', 5, ''];
        yield 'multibyte' => ['é', 4, 'éééé'];
    }

    #[DataProvider('repeatProvider')]
    public function testRepeat(string $input, int $times, string $expected): void
    {
        self::assertSame($expected, Str::repeat($input, $times));
    }

    public function testRepeatNegativeTimesThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::repeat('x', -1);
    }

    // ─────────────────────────────────────────────────────────────────────
    // shuffle
    // ─────────────────────────────────────────────────────────────────────

    public function testShuffleEmpty(): void
    {
        self::assertSame('', Str::shuffle(''));
    }

    public function testShufflePreservesCharacters(): void
    {
        $input = 'abcdefgh';
        $shuffled = Str::shuffle($input);

        self::assertSame(strlen($input), strlen($shuffled));
        $original = str_split($input);
        $result = str_split($shuffled);
        sort($original);
        sort($result);
        self::assertSame($original, $result);
    }

    public function testShufflePreservesMultibyteCharacters(): void
    {
        $input = 'café';
        $shuffled = Str::shuffle($input);

        self::assertSame(Str::length($input), Str::length($shuffled));
        $original = mb_str_split($input, 1, 'UTF-8');
        $result = mb_str_split($shuffled, 1, 'UTF-8');
        sort($original);
        sort($result);
        self::assertSame($original, $result);
    }

    // ─────────────────────────────────────────────────────────────────────
    // pad
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: int, 2: string, 3: Side, 4: string}>
     */
    public static function padProvider(): iterable
    {
        // Default side: End
        yield 'pad end default' => ['hi', 5, ' ', Side::End, 'hi   '];
        yield 'pad start' => ['5', 3, '0', Side::Start, '005'];
        yield 'pad both even' => ['hi', 6, '-', Side::Both, '--hi--'];
        yield 'pad both odd extra on end' => ['hi', 5, '-', Side::Both, '-hi--'];

        // Already long enough
        yield 'already at length' => ['hello', 5, ' ', Side::End, 'hello'];
        yield 'already longer' => ['hello world', 5, ' ', Side::End, 'hello world'];

        // Repeat pattern
        yield 'pad with longer pattern end' => ['hi', 7, 'abc', Side::End, 'hiabcab'];
        yield 'pad with longer pattern start' => ['hi', 7, 'abc', Side::Start, 'abcabhi'];

        // Multibyte
        yield 'multibyte pad' => ['café', 6, '.', Side::End, 'café..'];
        yield 'multibyte pad start' => ['café', 6, '*', Side::Start, '**café'];
        yield 'multibyte pattern' => ['x', 4, '·', Side::Both, '·x··'];

        // Edge: empty padding string is noop
        yield 'empty pattern is noop' => ['hi', 10, '', Side::End, 'hi'];

        // Edge: empty input
        yield 'empty input padded' => ['', 3, 'x', Side::End, 'xxx'];
    }

    #[DataProvider('padProvider')]
    public function testPad(string $value, int $length, string $with, Side $side, string $expected): void
    {
        self::assertSame($expected, Str::pad($value, $length, $with, $side));
    }

    public function testPadDefaultsToEndSideAndSpace(): void
    {
        self::assertSame('hi   ', Str::pad('hi', 5));
    }

    // ─────────────────────────────────────────────────────────────────────
    // wrap
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: int, 2: string, 3: bool, 4: string}>
     */
    public static function wrapProvider(): iterable
    {
        yield 'simple wrap' => [
            'The quick brown fox',
            10,
            "\n",
            false,
            "The quick\nbrown fox",
        ];
        yield 'no wrap needed' => [
            'short',
            20,
            "\n",
            false,
            'short',
        ];
        yield 'long word kept whole when cutLongWords is false' => [
            'antidisestablishmentarianism is a word',
            5,
            "\n",
            false,
            "antidisestablishmentarianism\nis a\nword",
        ];
        yield 'long word cut when cutLongWords is true' => [
            'antidisestablishmentarianism',
            6,
            "\n",
            true,
            "antidi\nsestab\nlishme\nntaria\nnism",
        ];
        yield 'custom break sequence' => [
            'a b c d',
            3,
            '|',
            false,
            'a b|c d',
        ];
        yield 'empty input' => ['', 5, "\n", false, ''];
        yield 'multiple existing newlines preserved' => [
            "line one is long\nline two",
            10,
            "\n",
            false,
            "line one\nis long\nline two",
        ];
    }

    #[DataProvider('wrapProvider')]
    public function testWrap(string $value, int $width, string $break, bool $cut, string $expected): void
    {
        self::assertSame($expected, Str::wrap($value, $width, $break, $cut));
    }

    // ─────────────────────────────────────────────────────────────────────
    // chunk
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{0: string, 1: int, 2: string, 3: string}>
     */
    public static function chunkProvider(): iterable
    {
        yield 'three-char chunks with dash' => ['abcdefghij', 3, '-', 'abc-def-ghi-j-'];
        yield 'exact multiple' => ['abcdefghi', 3, '-', 'abc-def-ghi-'];
        yield 'default separator and length' => [
            str_repeat('a', 80),
            76,
            "\r\n",
            str_repeat('a', 76) . "\r\n" . 'aaaa' . "\r\n",
        ];
        yield 'multibyte chunks' => ['ééééé', 2, '|', 'éé|éé|é|'];
        yield 'empty value' => ['', 3, '-', ''];
        yield 'single chunk' => ['ab', 5, '-', 'ab-'];
    }

    #[DataProvider('chunkProvider')]
    public function testChunk(string $value, int $length, string $separator, string $expected): void
    {
        self::assertSame($expected, Str::chunk($value, $length, $separator));
    }

    public function testChunkZeroLengthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::chunk('hello', 0);
    }

    public function testChunkNegativeLengthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::chunk('hello', -1);
    }

    public function testWrapZeroWidthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::wrap('hello', 0);
    }

    public function testWrapNegativeWidthThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::wrap('hello', -1);
    }

    public function testWrapPreservesEmptyLines(): void
    {
        // Triggers the "empty line" branch inside wrapLine.
        self::assertSame("a\n\nb", Str::wrap("a\n\nb", 10));
    }

    public function testWrapCutLongWordAfterContent(): void
    {
        // Triggers the "currentLength > 0" branch inside cutLongWords handling.
        self::assertSame(
            "ok\nantidi\nsestab\nlish",
            Str::wrap('ok antidisestablish', 6, "\n", true),
        );
    }

    public function testPadExactlyMultipleOfPattern(): void
    {
        // Padding length is a clean multiple of the pattern length — exercises buildFiller exact path.
        self::assertSame('xxxhi', Str::pad('hi', 5, 'x', Side::Start));
    }

    public function testPadBothOddOnePadding(): void
    {
        // Triggers buildFiller(0) on the start side and buildFiller(1) on the end side.
        self::assertSame('hi-', Str::pad('hi', 3, '-', Side::Both));
    }

    // ─────────────────────────────────────────────────────────────────────
    // increment
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function incrementProvider(): iterable
    {
        yield 'a to b' => ['a', 'b'];
        yield 'z carries to aa' => ['z', 'aa'];
        yield 'Z carries to AA' => ['Z', 'AA'];
        yield '9 carries to 10' => ['9', '10'];
        yield 'Az to Ba' => ['Az', 'Ba'];
        yield 'Zz to AAa' => ['Zz', 'AAa'];
        yield 'A9 to B0' => ['A9', 'B0'];
        yield 'middle digit' => ['ab1', 'ab2'];
    }

    #[DataProvider('incrementProvider')]
    public function testIncrement(string $input, string $expected): void
    {
        self::assertSame($expected, Str::increment($input));
    }

    public function testIncrementEmptyThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::increment('');
    }

    public function testIncrementNonAlphanumericThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::increment('a-b');
    }

    // ─────────────────────────────────────────────────────────────────────
    // decrement
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function decrementProvider(): iterable
    {
        yield 'b to a' => ['b', 'a'];
        yield 'B to A' => ['B', 'A'];
        yield '2 to 1' => ['2', '1'];
        yield 'aa to z' => ['aa', 'z'];
        yield 'Ba to Az' => ['Ba', 'Az'];
        yield 'middle digit' => ['ab2', 'ab1'];
    }

    #[DataProvider('decrementProvider')]
    public function testDecrement(string $input, string $expected): void
    {
        self::assertSame($expected, Str::decrement($input));
    }

    public function testDecrementMinimumThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::decrement('a');
    }

    public function testDecrementEmptyThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::decrement('');
    }

    public function testDecrementLeadingZeroThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::decrement('01');
    }

    public function testDecrementNonAlphanumericThrows(): void
    {
        $this->expectException(\ValueError::class);
        Str::decrement('a-b');
    }
}
