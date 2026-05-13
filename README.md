# Phyx

[![CI](https://github.com/antharuu/Phyx/actions/workflows/ci.yml/badge.svg)](https://github.com/antharuu/Phyx/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/antharuu/phyx.svg)](https://packagist.org/packages/antharuu/phyx)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/antharuu/phyx/php.svg)](https://packagist.org/packages/antharuu/phyx)
[![License](https://img.shields.io/packagist/l/antharuu/phyx.svg)](LICENSE)

Phyx is a modern PHP utility library that rethinks native PHP functions around a more coherent, readable, and predictable API.

It keeps the good parts of PHP: arrays, strings, paths, URLs, JSON, HTML, numbers, bytes. It removes as much historical friction as possible: inconsistent argument order, `false|int` return values, magic flags, multibyte traps, ambiguous parsing, and function names that were never designed as one standard library.

```php
use Phyx\Str;
use Phyx\Arr;
use Phyx\Json;
use Phyx\Url;
use Phyx\Path;
use Phyx\Enums\CaseSensitivity;

Str::contains('Hello World', 'world', CaseSensitivity::Insensitive); // true
Arr::getPath($payload, 'user.profile.name', 'Guest');                // 'Guest'
Json::tryDecodeArray($json);                                         // ?array
Url::withQueryValue($url, 'page', 2);                                 // rebuilt URL
Path::join('/var', 'www', 'app');                                     // '/var/www/app'
```

Phyx is not a framework, not a runtime, and not a new language. It is a pragmatic standard-library layer for everyday PHP code.

## Why Phyx?

PHP already has the building blocks. Phyx makes them feel designed together.

```php
use Phyx\Str;
use Phyx\Enums\CaseSensitivity;

// Native PHP asks you to remember argument order and sentinel values.
$position = strpos($haystack, $needle); // int|false

if ($position !== false) {
    // ...
}

// Phyx keeps the target value first and returns the shape you expect.
$position = Str::indexOf($haystack, $needle); // ?int

if ($position !== null) {
    // ...
}

Str::contains('Invoice.pdf', 'invoice', CaseSensitivity::Insensitive); // true, explicit policy
```

A few rules guide the whole library:

- target value first: string first, array first, URL first, path first;
- predictable missing values: `null` instead of PHP's `false|int` style sentinels;
- explicit enums instead of boolean soup and integer flags when they clarify intent;
- multibyte string operations by default;
- pure helpers first: methods return values instead of printing, mutating, or hiding side effects;
- small façades over focused traits, so each domain stays organized internally.

## Install

```bash
composer require antharuu/phyx
```

Requirements:

- PHP `^8.1`
- `ext-intl`
- `ext-mbstring`

PHP 8.3 conveniences such as `json_validate`, `str_increment`, and `str_decrement` are polyfilled where Phyx needs them, so the public API remains available on every supported PHP version.

## Stability

Phyx is preparing its first public release as `v0.1.0`. The library is tested, statically analyzed, and intended for real projects, but the public API may still receive small adjustments before `1.0.0`.

## The surface area

Phyx is organized by everyday domains rather than by PHP's historical function families.

| Façade | Focus |
| --- | --- |
| `Phyx\Str` | strings, casing, search, slicing, replace, formatting, hashes, HTML-safe helpers |
| `Phyx\Arr` | array access, paths, transforms, grouping, sorting, set operations, shape changes |
| `Phyx\Json` | encode/decode, validation, typed decode helpers, lightweight path access |
| `Phyx\Html` | escaping, entities, tags, attributes, simple fragments |
| `Phyx\Url` | parse, build, query strings, encoding, validation, normalization, comparison |
| `Phyx\Path` | joining, normalization, segments, extensions, filesystem-aware checks |
| `Phyx\Num` | aggregates, predicates, formatting, ranges, rounding, trigonometry |
| `Phyx\Bytes` | binary-safe length, hex, base64, packing, random bytes, checksums |

This README shows the shape of the API. The complete method reference lives in `docs/`.

## Strings that behave like strings

`Phyx\Str` is the largest façade. It wraps common string work with consistent naming, multibyte behavior, and explicit options.

```php
use Phyx\Str;
use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Ordering;
use Phyx\Enums\Side;

// Search and extraction.
Str::contains('Hello World', 'world', CaseSensitivity::Insensitive); // true
Str::startsWith('composer.json', 'composer');                        // true
Str::indexOf('café', 'fé');                                          // 2
Str::before('https://phyx.dev/docs', '://');                         // 'https'
Str::after('hello@phyx.dev', '@');                                   // 'phyx.dev'
Str::afterLast('/var/www/index.php', '/');                           // 'index.php'

// Multibyte-safe casing and length.
Str::lower('ÉCOLE');                                                 // 'école'
Str::capitalize('élise');                                            // 'Élise'
Str::length('café');                                                 // 4

// Slicing, replacing, trimming, padding.
Str::slice('Hello World', 6);                                        // 'World'
Str::replace('Hello World', 'World', 'Phyx');                        // 'Hello Phyx'
Str::trim('--draft--', '-', Side::End);                              // '--draft'
Str::pad('42', 5, '0', Side::Start);                                 // '00042'

// Comparison is configurable without multiplying method names.
Str::compare('item2', 'item10', ordering: Ordering::Natural);         // -1
Str::compare('Apple', 'apple', CaseSensitivity::Insensitive);         // 0

// Encoding, hashes and safe HTML output.
Str::toHex('abc');                                                   // '616263'
Str::fromHex('616263');                                              // 'abc'
Str::md5('hello');                                                   // '5d41402abc4b2a76b9719d911017c592'
Str::escapeHtml('<b>Tom & Jerry</b>');                               // '&lt;b&gt;Tom &amp; Jerry&lt;/b&gt;'
```

## Arrays without hidden mutation

`Phyx\Arr` treats arrays as values. Access, path updates and transformations return a result; they do not mutate your original array by surprise.

```php
use Phyx\Arr;
use Phyx\Enums\Comparison;
use Phyx\Enums\SortDirection;

$user = [
    'id' => 42,
    'profile' => ['name' => 'Ada'],
    'roles' => ['admin', 'editor'],
];

Arr::get($user, 'id');                         // 42
Arr::getPath($user, 'profile.name');           // 'Ada'
Arr::getPath($user, 'profile.avatar', 'none'); // 'none'
Arr::hasPath($user, 'roles.0');                // true

$updated = Arr::setPath($user, 'profile.name', 'Grace');

$user['profile']['name'];    // 'Ada'
$updated['profile']['name']; // 'Grace'

Arr::contains(['1', 2, 3], 1, Comparison::Strict); // false
Arr::pluck($rows, 'email');                        // list of emails
Arr::groupBy($orders, fn ($order) => $order['status']);
Arr::sortBy($users, fn ($user) => $user['name'], SortDirection::Ascending);
Arr::dot(['user' => ['name' => 'Ada']]);           // ['user.name' => 'Ada']
```

## JSON with clear failure modes

Native JSON functions mix optional exceptions, global error state, flags, and ambiguous `null` values. Phyx separates the strict methods from the safe `try*` methods.

```php
use Phyx\Json;

$json = '{"user":{"name":"Ada","active":true}}';

Json::isValid($json);                     // true
Json::decodeArray($json);                 // ['user' => ['name' => 'Ada', 'active' => true]]
Json::get($json, 'user.name');            // 'Ada'
Json::has($json, 'user.active');          // true

Json::set($json, 'user.name', 'Grace');   // '{"user":{"name":"Grace","active":true}}'
Json::remove($json, 'user.active');       // '{"user":{"name":"Ada"}}'

Json::tryDecodeArray('{broken json');     // null
Json::pretty(['name' => 'Ada']);          // formatted JSON string
```

## URLs that are more than strings

`Phyx\Url` keeps parsing, components, query strings, encoding, validation and normalization in one vocabulary.

```php
use Phyx\Url;
use Phyx\Enums\QueryFormat;

$url = 'https://example.com/docs?lang=en#intro';

Url::scheme($url);                                  // 'https'
Url::host($url);                                    // 'example.com'
Url::path($url);                                    // '/docs'
Url::queryParameters($url);                         // ['lang' => 'en']
Url::isHttps($url);                                 // true

Url::withPath($url, '/guides');                     // 'https://example.com/guides?lang=en#intro'
Url::withQueryValue($url, 'page', 2);               // 'https://example.com/docs?lang=en&page=2#intro'
Url::withoutFragment($url);                         // 'https://example.com/docs?lang=en'
Url::encodeComponent('a value/with slash');         // 'a%20value%2Fwith%20slash'
Url::buildQuery(['q' => 'php helpers'], QueryFormat::Rfc3986);
```

## Paths without filesystem confusion

`Phyx\Path` separates syntactic path manipulation from methods that actually touch the filesystem.

```php
use Phyx\Path;
use Phyx\Enums\PathStyle;

Path::join('/var', 'www', 'app');          // '/var/www/app'
Path::normalize('/var/www/../log');        // '/var/log'
Path::extension('/tmp/archive.tar.gz');    // 'gz'
Path::filename('/tmp/archive.tar.gz');     // 'archive.tar'
Path::segments('/var/www/app');            // ['var', 'www', 'app']

Path::toWindows('/var/www/app');           // '\\var\\www\\app'
Path::normalize('C:\\temp\\..\\app', PathStyle::Windows);

Path::exists('/var/www/app');              // explicit filesystem check
Path::real('/var/www/../log');             // ?string
```

## HTML, numbers and bytes included

The smaller façades follow the same philosophy: concise names, clear returns, explicit options.

```php
use Phyx\Html;
use Phyx\Num;
use Phyx\Bytes;

Html::escape('<a href="/">Home</a>');        // '&lt;a href=&quot;/&quot;&gt;Home&lt;/a&gt;'
Html::attribute('disabled', true);            // 'disabled'
Html::tag('strong', 'Warning');               // '<strong>Warning</strong>'

Num::average([10, 20, 30]);                   // 20.0
Num::clamp(120, 0, 100);                      // 100
Num::percentage(25, 200);                     // 12.5
Num::ordinal(3);                              // '3rd'

Bytes::length("\x00\x01\x02");              // 3
Bytes::toHex('abc');                          // '616263'
Bytes::fromHex('616263');                     // 'abc'
Bytes::toBase64Url('hello');                  // 'aGVsbG8'
Bytes::fromInts([2, 1]);                      // "\x02\x01"
```

## Design principles

Phyx deliberately stays boring in the best way:

- static façades: easy to import, easy to read, no container required;
- focused domains: `Str`, `Arr`, `Json`, `Url`, `Path`, `Html`, `Num`, `Bytes`;
- orthogonal enums: one method with an explicit policy beats five near-duplicates;
- documented edge cases: every public method carries PHPDoc with examples and native references;
- no surprise I/O: helpers return data unless their name clearly says they inspect the filesystem;
- no framework coupling: Phyx is meant to fit into plain PHP, libraries, CLIs and applications.

## Documentation

The README is a tour, not the manual. For full method lists, signatures, return types and edge cases, see:

- [`docs/Str.md`](docs/Str.md)
- [`docs/Arr.md`](docs/Arr.md)
- [`docs/Json.md`](docs/Json.md)
- [`docs/Html.md`](docs/Html.md)
- [`docs/Url.md`](docs/Url.md)
- [`docs/Path.md`](docs/Path.md)
- [`docs/Num.md`](docs/Num.md)
- [`docs/Bytes.md`](docs/Bytes.md)

## Release notes

See [`CHANGELOG.md`](CHANGELOG.md) for tagged release history.

## License

Phyx is released under the [MIT License](LICENSE).

## Development

```bash
composer install
composer test       # PHPUnit
composer analyse    # PHPStan level 8
composer coverage   # HTML + Clover coverage report, requires Xdebug or PCOV
composer check      # tests + analysis + coverage gate
```

Phyx aims to keep its public API clean enough for open-source use: readable names, complete PHPDoc, tests, static analysis, and predictable behavior before cleverness.
