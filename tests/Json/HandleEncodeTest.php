<?php

declare(strict_types=1);

namespace Phyx\Tests\Json;

use JsonException;
use Phyx\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Json::class)]
final class HandleEncodeTest extends TestCase
{
    public function testEncodeReturnsCompactJsonAndThrowsOnError(): void
    {
        self::assertSame('{"name":"Ada","active":true}', Json::encode(['name' => 'Ada', 'active' => true]));

        $this->expectException(JsonException::class);
        Json::encode(fopen('php://memory', 'rb'));
    }

    public function testEncodeSupportsUnicodeAndSlashFlags(): void
    {
        self::assertSame('"\\u00e9\\/docs"', Json::encode('é/docs'));
        self::assertSame('"é/docs"', Json::encode('é/docs', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function testPrettyAddsPrettyPrintFlag(): void
    {
        self::assertSame("{\n    \"answer\": 42\n}", Json::pretty(['answer' => 42]));
    }

    public function testTryEncodeReturnsNullOnError(): void
    {
        self::assertNull(Json::tryEncode(fopen('php://memory', 'rb')));
    }
}
