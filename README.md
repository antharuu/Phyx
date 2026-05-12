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

## Development

```bash
composer install
composer test
composer analyse
composer check
```
