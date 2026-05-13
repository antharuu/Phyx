# Changelog

All notable changes to Phyx will be documented in this file.

Phyx follows [Semantic Versioning](https://semver.org/) for tagged releases. Until `1.0.0`, minor versions may still include public API adjustments while the library settles.

## [0.1.0] - 2026-05-13

### Added

- Initial public release of Phyx as a PHP `^8.1` utility library.
- Static façades for everyday PHP domains: `Str`, `Arr`, `Json`, `Html`, `Url`, `Path`, `Num`, and `Bytes`.
- Consistent value-first APIs around strings, arrays, JSON, URLs, paths, HTML, numbers, and byte strings.
- Explicit enums for policies such as case sensitivity, ordering, rounding, sorting, encoding, query formatting, and path style.
- PHP 8.1-compatible polyfills used internally for newer PHP conveniences required by the public API.
- PHPUnit, PHPStan level 8, GitHub Actions matrix testing from PHP 8.1 to 8.4, and full coverage gating.

### Quality

- Composer metadata prepared for Packagist publication.
- MIT license added.
- Release archives configured to exclude development-only files.
