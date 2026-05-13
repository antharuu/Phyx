# Security Policy

## Supported Versions

Phyx follows semantic versioning for tagged releases. Security fixes are applied to the latest released minor line whenever practical.

| Version | Supported |
| ------- | --------- |
| `0.1.x` | Yes |
| `< 0.1` | No |

Until `1.0.0`, the public API may still evolve, but security fixes are treated as high priority regardless of API stability.

## Reporting a Vulnerability

Please do not open a public issue for a security vulnerability.

Use GitHub's private vulnerability reporting for this repository if it is available. If it is not available, contact the maintainer privately through the GitHub profile and include only enough information to establish a secure reporting channel.

When reporting, please include:

- the affected Phyx version or commit;
- the affected API or component;
- a clear description of the issue;
- reproduction steps or a proof of concept, if safe to share privately;
- the expected impact.

## Response Expectations

The maintainer will try to acknowledge valid reports as quickly as possible. Confirmed vulnerabilities will be fixed in a private branch when appropriate, released as a tagged patch version, and documented with enough detail for users to upgrade safely.

## Scope

Security reports are in scope when they affect Phyx itself, including:

- unsafe escaping or encoding behavior;
- unexpected filesystem/path handling with security impact;
- parsing or normalization behavior that can lead to bypasses;
- dependency or packaging issues that affect consumers.

General usage questions, feature requests, and non-security bugs should use the normal issue templates.
