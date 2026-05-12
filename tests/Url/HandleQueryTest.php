<?php

declare(strict_types=1);

namespace Phyx\Tests\Url;

use Phyx\Enums\QueryFormat;
use Phyx\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class HandleQueryTest extends TestCase
{
    public function testParseQueryIsSafeAndAcceptsLeadingQuestionMark(): void
    {
        self::assertSame(['a' => '1', 'space' => 'a b', 'empty' => ''], Url::parseQuery('?a=1&space=a+b&empty='));
        self::assertSame([], Url::parseQuery(''));
    }

    public function testBuildQuerySupportsRfc1738AndRfc3986(): void
    {
        $parameters = ['space' => 'a b', 'nested' => ['x' => 'y']];

        self::assertSame('space=a+b&nested%5Bx%5D=y', Url::buildQuery($parameters, QueryFormat::Rfc1738));
        self::assertSame('space=a%20b&nested%5Bx%5D=y', Url::buildQuery($parameters, QueryFormat::Rfc3986));
    }

    public function testQueryParametersAndQueryMutations(): void
    {
        $url = 'https://example.com/path?b=2&a=1#frag';

        self::assertSame(['b' => '2', 'a' => '1'], Url::queryParameters($url));
        self::assertSame('https://example.com/path?x=10&y=yes#frag', Url::withQuery($url, ['x' => 10, 'y' => 'yes']));
        self::assertSame('https://example.com/path#frag', Url::withQuery($url, []));
        self::assertSame('https://example.com/path?b=2&a=3#frag', Url::withQueryValue($url, 'a', 3));
        self::assertSame('https://example.com/path?b=2#frag', Url::withoutQueryValue($url, 'a'));
    }
}
