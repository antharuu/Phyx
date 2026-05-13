# Contributing to Phyx

Thanks for considering a contribution to Phyx.

Phyx is a PHP `^8.1` utility library that wraps everyday PHP operations in coherent, predictable, value-first APIs. Contributions should preserve that positioning: small public surfaces, explicit behavior, strong documentation, tests, and compatibility across the supported PHP versions.

## Requirements

- PHP `^8.1`
- Composer
- `ext-intl`
- `ext-mbstring`
- Xdebug or PCOV when running coverage locally

Install dependencies:

```bash
composer install
```

## Development Commands

```bash
composer validate --strict
composer test
composer analyse
composer coverage:check
composer check
```

`composer check` runs tests, static analysis, and the coverage gate. The CI matrix also verifies PHP 8.1, 8.2, 8.3, and 8.4.

## Contribution Workflow

1. Open or find an issue that describes the change.
2. Fork the repository and create a focused branch.
3. Keep the change small and cohesive.
4. Add or update tests for every behavior change.
5. Update PHPDoc and docs when public APIs change.
6. Run the validation commands locally.
7. Open a pull request with a clear description and test plan.

## Public API Guidelines

Public APIs should be:

- compatible with PHP 8.1;
- static, explicit, and easy to read at call sites;
- predictable about empty inputs, missing values, key preservation, and return types;
- documented with behavior, edge cases, examples, and `@throws` where relevant;
- covered by tests, including edge cases and failure modes.

Avoid adding a method only because another framework has it. A Phyx helper should make native PHP usage clearer, safer, or more consistent.

## Compatibility

Do not call PHP 8.2+ / 8.3+ / 8.4+ / 8.5+ native functions directly unless there is a PHP 8.1-compatible path or polyfill. Phyx's advertised runtime floor is PHP 8.1.

Examples:

- `json_validate()` needs a PHP 8.1-compatible polyfill path.
- PHP 8.4 array helpers such as `array_find()` cannot be required directly.
- PHP 8.5 helpers such as `array_first()` and `array_last()` cannot be required directly.

## Tests and Coverage

New behavior must have focused tests. Prefer tests that document observable behavior rather than implementation details.

The project currently enforces a strict coverage gate. If you add reachable code, add tests that cover it instead of lowering thresholds or excluding meaningful code.

## Documentation

When changing public APIs, update the relevant file in `docs/` and, if the change is important for users, the README.

README examples should stay curated and marketing-oriented. Full method-level details belong in `docs/` and PHPDoc.

## Commit and PR Style

Use clear, conventional-style commit messages when practical:

```text
feat: add array collection helpers
fix: normalize URL path handling
docs: document Arr selector behavior
chore: prepare package release
```

Pull requests should include:

- what changed;
- why it changed;
- how it was tested;
- any compatibility or documentation impact.

## Security

Please do not disclose vulnerabilities publicly. See `SECURITY.md` for the reporting process.
