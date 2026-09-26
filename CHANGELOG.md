# Changelog

## 2.0.0 — Unreleased

Breaking redesign as a standalone secure-token library with an optional modern Laravel provider.

- Replace timestamp hashing with CSPRNG-backed alphanumeric, custom-alphabet, hexadecimal and Base64URL output.
- Enforce minimum entropy and bounded lengths/batches; support explicit non-secret prefixes.
- Add SHA-256 token digests, optional HMAC pepper and constant-time digest verification.
- Add strict CLI argument validation, machine-readable output and clear exit codes.
- Remove JWT auth endpoints, users migration, global helpers, secret logging and application-global provider changes.
- Add unit/integration tests, compatibility CI, installation smoke tests and migration documentation.

The Composer name and namespace remain for discoverability; existing HTTP integrations require migration.
