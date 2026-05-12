<?php

declare(strict_types=1);

namespace Phyx\Tests\Str;

use Phyx\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Str::class)]
final class HandleHashTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'phyx_hash_');
        if ($tempPath === false) {
            self::fail('Failed to create temp file for hash tests');
        }
        $this->tempFile = $tempPath;
        file_put_contents($this->tempFile, 'hello');
    }

    protected function tearDown(): void
    {
        if (is_file($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // md5 / md5File
    // ─────────────────────────────────────────────────────────────────────

    public function testMd5KnownDigest(): void
    {
        self::assertSame('5d41402abc4b2a76b9719d911017c592', Str::md5('hello'));
    }

    public function testMd5Empty(): void
    {
        self::assertSame('d41d8cd98f00b204e9800998ecf8427e', Str::md5(''));
    }

    public function testMd5File(): void
    {
        self::assertSame('5d41402abc4b2a76b9719d911017c592', Str::md5File($this->tempFile));
    }

    public function testMd5FileMissingReturnsNull(): void
    {
        self::assertNull(Str::md5File('/no/such/file/anywhere'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // sha1 / sha1File
    // ─────────────────────────────────────────────────────────────────────

    public function testSha1KnownDigest(): void
    {
        self::assertSame('aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d', Str::sha1('hello'));
    }

    public function testSha1Empty(): void
    {
        self::assertSame('da39a3ee5e6b4b0d3255bfef95601890afd80709', Str::sha1(''));
    }

    public function testSha1File(): void
    {
        self::assertSame('aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d', Str::sha1File($this->tempFile));
    }

    public function testSha1FileMissingReturnsNull(): void
    {
        self::assertNull(Str::sha1File('/no/such/file/anywhere'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // crc32
    // ─────────────────────────────────────────────────────────────────────

    public function testCrc32KnownValue(): void
    {
        self::assertSame(907060870, Str::crc32('hello'));
    }

    public function testCrc32Empty(): void
    {
        self::assertSame(0, Str::crc32(''));
    }

    // ─────────────────────────────────────────────────────────────────────
    // crypt
    // ─────────────────────────────────────────────────────────────────────

    public function testCryptIsDeterministic(): void
    {
        $salt = '$2y$04$abcdefghijklmnopqrstuv';
        $first = Str::crypt('secret', $salt);
        $second = Str::crypt('secret', $salt);

        self::assertSame($first, $second);
        self::assertNotEmpty($first);
    }

    // ─────────────────────────────────────────────────────────────────────
    // soundex
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function soundexProvider(): iterable
    {
        yield 'Robert' => ['Robert', 'R163'];
        yield 'Rupert same key as Robert' => ['Rupert', 'R163'];
        yield 'Tymczak' => ['Tymczak', 'T522'];
        yield 'empty returns all-zeros key' => ['', '0000'];
    }

    #[DataProvider('soundexProvider')]
    public function testSoundex(string $input, string $expected): void
    {
        self::assertSame($expected, Str::soundex($input));
    }

    // ─────────────────────────────────────────────────────────────────────
    // metaphone
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return iterable<string, array{string, int, string}>
     */
    public static function metaphoneProvider(): iterable
    {
        yield 'Thompson' => ['Thompson', 0, '0MPSN'];
        yield 'xylophone' => ['xylophone', 0, 'SLFN'];
        yield 'Thompson truncated' => ['Thompson', 3, '0MP'];
        yield 'empty returns empty' => ['', 0, ''];
    }

    #[DataProvider('metaphoneProvider')]
    public function testMetaphone(string $input, int $phonemes, string $expected): void
    {
        self::assertSame($expected, Str::metaphone($input, $phonemes));
    }
}
