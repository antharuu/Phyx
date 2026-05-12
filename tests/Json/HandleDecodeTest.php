<?php

declare(strict_types=1);

namespace Phyx\Tests\Json;

use JsonException;
use Phyx\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Json::class)]
final class HandleDecodeTest extends TestCase
{
    public function testDecodeHandlesObjectArrayScalarAndJsonNull(): void
    {
        self::assertEquals((object) ['name' => 'Ada'], Json::decode('{"name":"Ada"}'));
        self::assertSame([1, 2, 3], Json::decode('[1,2,3]'));
        self::assertSame('hello', Json::decode('"hello"'));
        self::assertSame(42, Json::decode('42'));
        self::assertSame(1.5, Json::decode('1.5'));
        self::assertTrue(Json::decode('true'));
        self::assertNull(Json::decode('null'));
    }

    public function testDecodeThrowsOnInvalidJsonAndDepthExceeded(): void
    {
        $this->expectException(JsonException::class);
        Json::decode('{');
    }

    public function testDecodeThrowsWhenDepthIsExceeded(): void
    {
        $this->expectException(JsonException::class);
        Json::decode('{"a":{"b":true}}', 2);
    }

    public function testDecodeArrayReturnsAssociativeArrayForObjectsAndArrays(): void
    {
        self::assertSame(['name' => 'Ada'], Json::decodeArray('{"name":"Ada"}'));
        self::assertSame([1, 2, 3], Json::decodeArray('[1,2,3]'));
    }

    public function testDecodeArrayRejectsScalarsAndJsonNull(): void
    {
        $this->expectException(JsonException::class);
        Json::decodeArray('null');
    }

    public function testDecodeObjectReturnsStdClassOnly(): void
    {
        $decoded = Json::decodeObject('{"name":"Ada"}');

        self::assertInstanceOf(stdClass::class, $decoded);
        self::assertSame('Ada', $decoded->name);
    }

    public function testDecodeObjectRejectsArraysAndScalars(): void
    {
        $this->expectException(JsonException::class);
        Json::decodeObject('[1,2,3]');
    }

    public function testTryDecodeReturnsNullOnInvalidJsonButKeepsValidJsonNull(): void
    {
        self::assertNull(Json::tryDecode('{'));
        self::assertNull(Json::tryDecode('null'));
        self::assertSame(['ok' => true], Json::tryDecodeArray('{"ok":true}'));
        self::assertNull(Json::tryDecodeArray('42'));
        self::assertNull(Json::tryDecodeArray('{'));
    }
}
