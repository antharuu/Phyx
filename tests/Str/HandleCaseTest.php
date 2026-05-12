<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleCaseTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function lowerProvider(): iterable
    {
        yield 'ascii' => ['Hello WORLD', 'hello world'];
        yield 'already lower' => ['hello', 'hello'];
        yield 'multibyte accented' => ['ÉCOLE', 'école'];
        yield 'multibyte mixed' => ['Café CAFÉ', 'café café'];
        yield 'empty' => ['', ''];
        yield 'digits and punctuation unchanged' => ['A1!Z', 'a1!z'];
    }

    #[DataProvider('lowerProvider')]
    public function testLower(string $input, string $expected): void
    {
        self::assertSame($expected, Str::lower($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function upperProvider(): iterable
    {
        yield 'ascii' => ['Hello world', 'HELLO WORLD'];
        yield 'already upper' => ['HELLO', 'HELLO'];
        yield 'multibyte accented' => ['école', 'ÉCOLE'];
        yield 'multibyte mixed' => ['café CAFÉ', 'CAFÉ CAFÉ'];
        yield 'empty' => ['', ''];
        yield 'digits and punctuation unchanged' => ['a1!z', 'A1!Z'];
    }

    #[DataProvider('upperProvider')]
    public function testUpper(string $input, string $expected): void
    {
        self::assertSame($expected, Str::upper($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function capitalizeProvider(): iterable
    {
        yield 'ascii' => ['hello world', 'Hello world'];
        yield 'first already upper' => ['Hello world', 'Hello world'];
        yield 'multibyte first letter' => ['école', 'École'];
        yield 'rest untouched' => ['hELLO', 'HELLO'];
        yield 'empty' => ['', ''];
        yield 'single char ascii' => ['x', 'X'];
        yield 'single char multibyte' => ['é', 'É'];
    }

    #[DataProvider('capitalizeProvider')]
    public function testCapitalize(string $input, string $expected): void
    {
        self::assertSame($expected, Str::capitalize($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function decapitalizeProvider(): iterable
    {
        yield 'ascii' => ['Hello World', 'hello World'];
        yield 'first already lower' => ['hello world', 'hello world'];
        yield 'multibyte first letter' => ['École', 'école'];
        yield 'rest untouched' => ['Hello', 'hello'];
        yield 'empty' => ['', ''];
        yield 'single char ascii' => ['X', 'x'];
        yield 'single char multibyte' => ['É', 'é'];
    }

    #[DataProvider('decapitalizeProvider')]
    public function testDecapitalize(string $input, string $expected): void
    {
        self::assertSame($expected, Str::decapitalize($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function capitalizeWordsProvider(): iterable
    {
        yield 'ascii lowercase' => ['hello world', 'Hello World'];
        yield 'ascii mixed case' => ['hello WORLD', 'Hello World'];
        yield 'multibyte' => ['école polytechnique', 'École Polytechnique'];
        yield 'single word' => ['phyx', 'Phyx'];
        yield 'empty' => ['', ''];
        yield 'multiple spaces' => ['hello  world', 'Hello  World'];
    }

    #[DataProvider('capitalizeWordsProvider')]
    public function testCapitalizeWords(string $input, string $expected): void
    {
        self::assertSame($expected, Str::capitalizeWords($input));
    }
}
