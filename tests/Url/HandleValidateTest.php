<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Enums\UrlValidation;
use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleValidateTest extends TestCase
{
    public function testValidatesAbsoluteRelativeAndHttpUrls(): void
    {
        self::assertTrue(Url::isValid('https://example.com/a?b=1', UrlValidation::Absolute));
        self::assertFalse(Url::isValid('/relative/path', UrlValidation::Absolute));

        self::assertTrue(Url::isValid('/relative/path?x=1', UrlValidation::Relative));
        self::assertFalse(Url::isValid('https://example.com', UrlValidation::Relative));

        self::assertTrue(Url::isValid('http://example.com', UrlValidation::Http));
        self::assertTrue(Url::isValid('https://example.com', UrlValidation::Http));
        self::assertFalse(Url::isValid('ftp://example.com', UrlValidation::Http));
    }

    public function testConveniencePredicates(): void
    {
        self::assertTrue(Url::isAbsolute('mailto:user@example.com'));
        self::assertFalse(Url::isAbsolute('/path'));
        self::assertTrue(Url::isRelative('../path'));
        self::assertFalse(Url::isRelative('//example.com/path'));
        self::assertTrue(Url::isHttp('http://example.com'));
        self::assertFalse(Url::isHttp('https://example.com'));
        self::assertTrue(Url::isHttps('https://example.com'));
        self::assertFalse(Url::isHttps('http://example.com'));
    }
}
