<?php

declare(strict_types=1);

namespace Phyx\Tests\Path;

use Phyx\Path;
use PHPUnit\Framework\TestCase;

final class HandleResolveTest extends TestCase
{
    public function testFilesystemMethodsAreExplicit(): void
    {
        $dir = sys_get_temp_dir() . '/phyx_path_' . bin2hex(random_bytes(4));
        mkdir($dir);
        $file = $dir . '/file.txt';
        file_put_contents($file, 'ok');

        try {
            self::assertTrue(Path::exists($dir));
            self::assertTrue(Path::isDirectory($dir));
            self::assertFalse(Path::isFile($dir));
            self::assertTrue(Path::isFile($file));
            self::assertSame(realpath($file), Path::real($file));
            self::assertNull(Path::real($dir . '/missing'));
            self::assertFalse(Path::exists($dir . '/missing'));
        } finally {
            @unlink($file);
            @rmdir($dir);
        }
    }
}
