<?php

declare(strict_types=1);

namespace Phyx\Tests\Json;

use JsonException;
use Phyx\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Json::class)]
final class HandleValidateTest extends TestCase
{
    public function testIsValidAcceptsAllJsonShapesIncludingNull(): void
    {
        self::assertTrue(Json::isValid('{"a":1}'));
        self::assertTrue(Json::isValid('[1,2,3]'));
        self::assertTrue(Json::isValid('"text"'));
        self::assertTrue(Json::isValid('null'));
        self::assertTrue(Json::isValid('false'));
    }

    public function testIsValidRejectsInvalidJsonAndExceededDepth(): void
    {
        self::assertFalse(Json::isValid('{"a":1}', 0));
        self::assertFalse(Json::isValid('{'));
        self::assertFalse(Json::isValid('{"a":{"b":true}}', 2));
    }

    public function testAssertValidRejectsInvalidDepth(): void
    {
        $this->expectException(JsonException::class);

        Json::assertValid('{"ok":true}', 0);
    }

    public function testAssertValidReturnsVoidForValidJson(): void
    {
        Json::assertValid('{"ok":true}');

        $this->addToAssertionCount(1);
    }

    public function testAssertValidThrowsForInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        Json::assertValid('{');
    }
}
