<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Enums\MatchMode;
use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleMatchTest extends TestCase
{
    public function testGlobReturnsListAndEmptyOnNoMatch(): void
    {
        $dir = sys_get_temp_dir() . '/phyx_path_glob_' . bin2hex(random_bytes(4));
        mkdir($dir);
        file_put_contents($dir . '/a.txt', 'a');
        file_put_contents($dir . '/b.log', 'b');

        try {
            self::assertSame([$dir . '/a.txt'], Path::glob($dir . '/*.txt'));
            self::assertSame([], Path::glob($dir . '/*.missing'));
        } finally {
            @unlink($dir . '/a.txt');
            @unlink($dir . '/b.log');
            @rmdir($dir);
        }
    }

    public function testMatchesSupportsGlobRegexAndLiteral(): void
    {
        self::assertTrue(Path::matches('src/Path.php', 'src/*.php'));
        self::assertFalse(Path::matches('src/Path.php', 'tests/*.php'));
        self::assertTrue(Path::matches('file123.txt', '/^file\d+\.txt$/', MatchMode::Regex));
        self::assertTrue(Path::matches('a*b', 'a*b', MatchMode::Literal));
        self::assertFalse(Path::matches('axb', 'a*b', MatchMode::Literal));
    }
}
