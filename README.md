# Phyx

Phyx is a modern PHP library that rethinks native functions around a more coherent, readable, and predictable API.

The goal is intentionally focused: keep PHP, but offer a cleaner surface for everyday operations that are currently spread across inconsistent native functions, argument orders, return values, and naming conventions.

Phyx is not a new language in this package. It is a pragmatic PHP library that starts by redesigning common native-function workflows into a clearer standard-library-style API.

## Current direction

- modern replacements around native PHP functions
- consistent naming and argument order
- predictable return values and behavior
- readable APIs first; chaining only when it genuinely helps
- no magic framework layer
- no broad runtime ambition for now

## At a glance — `Phyx\Str`

```php
use Phyx\Str;
use Phyx\Enums\CaseSensitivity;
use Phyx\Enums\Side;
use Phyx\Enums\Ordering;

// Search & inspect
Str::contains('Hello World', 'world', CaseSensitivity::Insensitive); // true
Str::indexOf('café', 'fé');                                          // 2
Str::before('https://phyx.dev', '://');                              // 'https'
Str::after('hello@phyx.dev', '@');                                   // 'phyx.dev'

// Trim & pad with explicit side
Str::trim('--abc--', '-', Side::Start);     // 'abc--'
Str::pad('5', 3, '0', Side::Start);         // '005'

// Casing, multibyte safe
Str::lower('ÉCOLE');                        // 'école'
Str::capitalize('école');                   // 'École'

// Slice, replace, split — value always first
Str::slice('Hello World', 6);               // 'World'
Str::replace('Hello World', 'World', 'Phyx'); // 'Hello Phyx'
Str::split('a,b,c', ',');                   // ['a', 'b', 'c']

// Compare with orthogonal enums (no *I/*Nat method zoo)
Str::compare('item2', 'item10', ordering: Ordering::Natural); // -1
Str::compare('Apple', 'apple', CaseSensitivity::Insensitive); // 0

// Encode / hash / HTML
Str::toHex('abc');                          // '616263'
Str::md5('hello');                          // '5d41402abc4b2a76b9719d911017c592'
Str::escapeHtml('<b>Tom & Jerry</b>');      // '&lt;b&gt;Tom &amp; Jerry&lt;/b&gt;'
```

The full method list lives in [`docs/Str.md`](docs/Str.md); each method has a complete PHPDoc with examples, edge cases and a `@see` link to its PHP-native equivalent.

## Why bother re-wrapping PHP?

A few invariants Phyx enforces and PHP doesn't:

- **`$value` always first.** No more `strpos($haystack, $needle)` vs `array_search($needle, $haystack)` mismatch.
- **Multibyte by default.** `Str::length('café')` returns `4`, not `5`. Encoding is picked through the `Encoding` enum, not a magic string.
- **`null` for "not found".** `Str::indexOf(...)` returns `?int`, never the PHP gotcha `false|int`.
- **No insensitive twins.** `contains`/`indexOf`/`compare` take a `CaseSensitivity` enum instead of pairing every method with a `*I` variant.
- **No side effects.** I/O variants (`printf`, `fprintf`, …) are deliberately absent; you compose the string and emit it yourself.

## Requirements

- PHP `^8.0`
- `ext-mbstring`

`str_increment` / `str_decrement` (PHP 8.3+) are polyfilled in `Phyx\Polyfills\AlphaIncrement` and used automatically when the native function is not available.

## Development

```bash
composer install
composer test       # PHPUnit
composer analyse    # PHPStan level 8
composer coverage   # HTML + clover coverage report (needs xdebug or pcov)
composer check      # test + analyse + 100% coverage gate
```
