<?php

declare(strict_types=1);

namespace Phyx\Tests\Json;

use JsonException;
use Phyx\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Json::class)]
final class HandleAccessTest extends TestCase
{
    private const DOCUMENT = '{"user":{"name":"Ada","email":null,"roles":["admin","dev"]}}';

    public function testGetReadsDotPathAndReturnsDefaultWhenMissing(): void
    {
        self::assertSame('Ada', Json::get(self::DOCUMENT, 'user.name'));
        self::assertSame('admin', Json::get(self::DOCUMENT, 'user.roles.0'));
        self::assertNull(Json::get(self::DOCUMENT, 'user.email', 'fallback'));
        self::assertSame('fallback', Json::get(self::DOCUMENT, 'user.missing', 'fallback'));
    }

    public function testHasDistinguishesMissingPathFromExistingNull(): void
    {
        self::assertTrue(Json::has(self::DOCUMENT, 'user.email'));
        self::assertFalse(Json::has(self::DOCUMENT, 'user.missing'));
    }

    public function testSetCreatesNestedPathsAndEncodesDocument(): void
    {
        self::assertSame('42', Json::set(self::DOCUMENT, '', 42));
        self::assertSame(
            '{"user":{"name":"Ada","email":null,"roles":["admin","dev"],"active":true}}',
            Json::set(self::DOCUMENT, 'user.active', true),
        );

        self::assertSame('{"a":{"b":1}}', Json::set('{}', 'a.b', 1));
        self::assertSame('["zero"]', Json::set('{}', 0, 'zero'));
        self::assertSame('{"a":["zero"]}', Json::set('{}', ['a', 0], 'zero'));
    }

    public function testRemoveDeletesExistingPathAndIgnoresMissingPath(): void
    {
        self::assertSame('null', Json::remove(self::DOCUMENT, ''));
        self::assertSame('{"user":{"name":"Ada","email":null}}', Json::remove(self::DOCUMENT, 'user.roles'));
        self::assertSame('{"user":{"name":"Ada","email":null,"roles":["admin"]}}', Json::remove(self::DOCUMENT, 'user.roles.1'));
        self::assertSame(self::DOCUMENT, Json::remove(self::DOCUMENT, 'user.missing'));
        self::assertSame(self::DOCUMENT, Json::remove(self::DOCUMENT, 'user.name.first'));
    }

    public function testAccessMethodsThrowOnInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        Json::get('{', 'anything');
    }
}
