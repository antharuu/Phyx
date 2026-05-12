<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Enums\Side;
use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleTrimTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: string, 2: string, 3: Side}>
     */
    public static function trimProvider(): iterable
    {
        // Default chars set, default Side::Both
        yield 'default whitespace both' => ['  hello  ', 'hello', " \t\n\r\0\x0B", Side::Both];
        yield 'default tabs and newlines' => ["\t\n  hello \r", 'hello', " \t\n\r\0\x0B", Side::Both];
        yield 'nothing to trim' => ['hello', 'hello', " \t\n\r\0\x0B", Side::Both];

        // Custom char set
        yield 'custom dashes both' => ['--abc--', 'abc', '-', Side::Both];
        yield 'custom dashes start' => ['--abc--', 'abc--', '-', Side::Start];
        yield 'custom dashes end' => ['--abc--', '--abc', '-', Side::End];

        // Multibyte
        yield 'multibyte both' => ['éàhelloàé', 'hello', 'éà', Side::Both];
        yield 'multibyte start' => ['éàhello', 'hello', 'éà', Side::Start];
        yield 'multibyte end' => ['helloàé', 'hello', 'éà', Side::End];

        // Regex metacharacters in $chars must not break the regex
        yield 'closing bracket in chars' => [']]abc]]', 'abc', ']', Side::Both];
        yield 'backslash in chars' => ['\\\\abc\\\\', 'abc', '\\', Side::Both];
        yield 'caret in chars' => ['^^abc^^', 'abc', '^', Side::Both];
        yield 'dash inside set' => ['-_abc_-', 'abc', '-_', Side::Both];

        // Edge cases
        yield 'empty value' => ['', '', '-', Side::Both];
        yield 'empty chars returns input' => ['  abc  ', '  abc  ', '', Side::Both];
        yield 'only-chars string trimmed to empty' => ['----', '', '-', Side::Both];
        yield 'single char value' => ['a', 'a', '-', Side::Both];
    }

    #[DataProvider('trimProvider')]
    public function testTrim(string $input, string $expected, string $chars, Side $side): void
    {
        self::assertSame($expected, Str::trim($input, $chars, $side));
    }

    public function testTrimDefaultsToBothSideAndWhitespace(): void
    {
        // Same call with no explicit chars / side
        self::assertSame('hello', Str::trim("  \t\nhello\r\n "));
    }

    public function testTrimReturnsInputWhenValueIsEmpty(): void
    {
        self::assertSame('', Str::trim(''));
    }

    public function testTrimReturnsInputWhenCharsAreEmpty(): void
    {
        // Verifies the early-return branch when $chars === ''
        self::assertSame('  abc  ', Str::trim('  abc  ', ''));
    }
}
