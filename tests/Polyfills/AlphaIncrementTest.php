<?php

declare(strict_types=1);

namespace Phyx\Tests\Polyfills;

use Phyx\Polyfills\AlphaIncrement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AlphaIncrement::class)]
final class AlphaIncrementTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function incrementProvider(): iterable
    {
        // Single character bumps
        yield 'a to b' => ['a', 'b'];
        yield 'y to z' => ['y', 'z'];
        yield 'A to B' => ['A', 'B'];
        yield 'Y to Z' => ['Y', 'Z'];
        yield '0 to 1' => ['0', '1'];
        yield '8 to 9' => ['8', '9'];

        // Single character with carry
        yield 'z carries to aa' => ['z', 'aa'];
        yield 'Z carries to AA' => ['Z', 'AA'];
        yield '9 carries to 10' => ['9', '10'];

        // Multi-character carries
        yield 'Az to Ba' => ['Az', 'Ba'];
        yield 'Zz to AAa' => ['Zz', 'AAa'];
        yield 'zz to aaa' => ['zz', 'aaa'];
        yield '99 to 100' => ['99', '100'];

        // Mixed alphanumeric
        yield 'A9 to B0' => ['A9', 'B0'];
        yield 'a9 to b0' => ['a9', 'b0'];
        yield 'middle digit unchanged' => ['ab1', 'ab2'];
        yield 'long with carry' => ['abz', 'aca'];

        // Class-preserving seed
        yield 'lowercase seed' => ['zz', 'aaa'];
        yield 'uppercase seed' => ['ZZ', 'AAA'];
        yield 'digit seed' => ['999', '1000'];
    }

    #[DataProvider('incrementProvider')]
    public function testIncrement(string $input, string $expected): void
    {
        self::assertSame($expected, AlphaIncrement::increment($input));
    }

    public function testIncrementEmptyThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::increment('');
    }

    public function testIncrementNonAlphanumericThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::increment('a-b');
    }

    public function testIncrementSpaceThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::increment('a b');
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function decrementProvider(): iterable
    {
        // Single character bumps
        yield 'b to a' => ['b', 'a'];
        yield 'z to y' => ['z', 'y'];
        yield 'B to A' => ['B', 'A'];
        yield 'Z to Y' => ['Z', 'Y'];
        yield '1 to 0' => ['1', '0'];
        yield '9 to 8' => ['9', '8'];

        // Multi-character borrows
        yield 'aa to z' => ['aa', 'z'];
        yield 'AA to Z' => ['AA', 'Z'];
        yield 'Ba to Az' => ['Ba', 'Az'];
        yield 'AAa to Zz' => ['AAa', 'Zz'];
        yield 'middle digit' => ['ab2', 'ab1'];
        yield 'long with borrow' => ['aca', 'abz'];
    }

    #[DataProvider('decrementProvider')]
    public function testDecrement(string $input, string $expected): void
    {
        self::assertSame($expected, AlphaIncrement::decrement($input));
    }

    public function testDecrementEmptyThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('');
    }

    public function testDecrementMinimumLowercaseThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('a');
    }

    public function testDecrementMinimumUppercaseThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('A');
    }

    public function testDecrementMinimumDigitThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('0');
    }

    public function testDecrementLeadingZeroThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('01');
    }

    public function testDecrementNonAlphanumericThrows(): void
    {
        $this->expectException(\ValueError::class);
        AlphaIncrement::decrement('a-b');
    }
}
