# Secure tokens for PHP & Laravel

[![CI](https://github.com/farhad-arjmand/lumen-hash-generator/actions/workflows/tests.yml/badge.svg)](https://github.com/farhad-arjmand/lumen-hash-generator/actions/workflows/tests.yml)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4)
[![MIT](https://img.shields.io/badge/license-MIT-blue)](LICENSE.md)

Generate unpredictable API tokens, invitation codes and public identifiers. Use the same small library in plain PHP, a Laravel application, or a terminal.

**This is the v2 development rewrite, not a tagged release.** The Composer package keeps its historical name, `farhad-arjmand/lumen-hash-generator`. It now generates random **tokens**, rather than hashing the current time. The legacy HTTP/JWT API is removed; read the [migration guide](UPGRADING.md) before upgrading.

```php
use FarhadArjmand\LumenHashGenerator\TokenGenerator;
use FarhadArjmand\LumenHashGenerator\TokenHasher;

$generator = new TokenGenerator();
$token = $generator->generate(prefix: 'api_'); // api_ + 32 random characters

$hasher = new TokenHasher();
$digest = $hasher->digest($token);            // Store this, not the bearer token.
$valid = $hasher->verify($token, $digest);     // Constant-time digest comparison.
```

## What it does

- Uses PHP's `random_bytes()` and unbiased `random_int()`. Fails closed if randomness is unavailable.
- Produces alphanumeric, custom-alphabet, hex and unpadded Base64URL tokens.
- Enforces at least 128 bits of entropy in the random portion, including custom alphabets.
- Supports prefixes and bounded batches; no timestamps, static salts or pseudo-random fallback.
- Provides SHA-256 token digests and optional HMAC-SHA-256 with a separate pepper.
- Has **no production Composer dependencies** for plain PHP.
- Integrates with Laravel through dependency injection and a namespaced configuration file.
- Does not create users, register HTTP routes, run migrations, log tokens or change application authentication.

## Install the development version

Until a v2 tag is published, explicitly opt into the rewrite branch in a test project:

```sh
composer config repositories.hash-generator vcs https://github.com/farhad-arjmand/lumen-hash-generator
composer require 'farhad-arjmand/lumen-hash-generator:dev-rewrite/secure-token-library'
```

Requirements: PHP 8.2–8.5. The optional provider is tested with Laravel 12 (PHP 8.2+) and Laravel 13 (PHP 8.3+). Laravel 5 and the old Lumen HTTP API are not supported by this rewrite. The core can be called directly from other frameworks, including modern Lumen installations; no Lumen integration is claimed or tested.

For development from a checkout:

```sh
composer install
composer test
composer lint
php examples/basic.php
```

## Choose a token format

| Method | Default random input | Default output length, excluding prefix |
|---|---|---|
| `generate()` | 32 independent Base62 characters | 32 characters, about 190 bits |
| `hex()` | 32 random bytes | 64 hexadecimal characters, 256 bits |
| `base64Url()` | 32 random bytes | 43 URL-safe characters, 256 bits |

```php
$generator->generate();
$generator->hex(bytes: 32, prefix: 'secret_');
$generator->base64Url(bytes: 32);
$generator->generate(26, TokenGenerator::HUMAN_READABLE, 'invite_');
$generator->batch(count: 10, prefix: 'key_');
```

`generate()` accepts 2–94 **distinct printable ASCII** alphabet characters, excluding spaces. Its random length must provide at least 128 bits and cannot exceed 4,096 characters. For example, a binary alphabet needs at least 128 characters; a decimal alphabet needs 39. The default alphabet and Base64URL are URL-safe; a custom alphabet is not necessarily URL-safe.

Hex/Base64URL accept 16–1,024 random bytes. Prefixes accept up to 64 ASCII letters, digits, `_` or `-`; prefixes add **no entropy**. Batches accept 1–1,000 tokens. Invalid options throw `InvalidArgumentException`; RNG failures propagate and must not be replaced with weaker randomness.

Randomness does not guarantee uniqueness. For persistent public identifiers, add a database unique constraint and retry a bounded number of times on a collision.

## Store and verify bearer tokens

```php
$hasher = new TokenHasher();
$plainText = $generator->base64Url();
$storedDigest = $hasher->digest($plainText);

// Persist the digest alongside owner, scope, expiration and revocation metadata.
// Return the plaintext token to the owner once, over HTTPS.

if (!$hasher->verify($providedToken, $storedDigest)) {
    // Reject the request.
}
```

Digests are 64 lowercase hexadecimal characters. `verify()` rejects empty tokens and malformed digests. Optional keyed storage:

```php
$hasher = new TokenHasher(pepper: $keyFromYourSecretManager); // At least 32 bytes.
```

Use a high-entropy key. Key length alone does not establish key entropy. Keep it separate from the database and stable across workers. Changing it invalidates existing digests unless your application implements a versioned key rotation strategy.

**This is not a password hasher, JWT issuer, encryption library or token-management service.** Use `password_hash()`/Laravel's password hashing for passwords. Authentication, authorization, scopes, expiry, rate limiting, revocation and one-time consumption belong in your application. Do not send token values to logs, analytics, exception context or URLs. Prefer an established authentication system such as Laravel Sanctum when you need a complete API-token lifecycle.

## Laravel

The service provider is discovered automatically. Constructor or route injection works with the concrete classes:

```php
use FarhadArjmand\LumenHashGenerator\TokenGenerator;

final class CreateInvitation
{
    public function __construct(private TokenGenerator $tokens) {}

    public function handle(): string
    {
        return $this->tokens->generate(26, TokenGenerator::HUMAN_READABLE, 'invite_');
    }
}
```

Configuration is optional:

```sh
php artisan vendor:publish --tag=hash-generator-config
```

The published `config/hash-generator.php` reads `HASH_GENERATOR_PEPPER`. Leave it unset for plain SHA-256 token digests. Config caching is supported. Rebuild configuration and restart long-lived workers after changing a pepper. The provider never replaces Laravel's `hashing` configuration or `Hash` facade.

For an HTTP use case, see [the application-owned route example](examples/laravel-route.php). It assumes Sanctum is already configured by the host application; the package does not install or configure it.

## CLI

```sh
vendor/bin/hash-generator --prefix=api_ --count=3
vendor/bin/hash-generator --format=hex --bytes=32
vendor/bin/hash-generator --format=base64url --count=2 --json
vendor/bin/hash-generator --alphabet=0123456789 --length=39
vendor/bin/hash-generator --help
```

Options use `--name=value`; `--json` returns an array even for one token. Without JSON, each token is a line on stdout. Exit codes: `0` success, `2` invalid arguments, `1` runtime failure. Invalid or failed requests produce no token output. Avoid redirecting stdout to shared CI logs.

## Verification and maintenance

CI runs unit and Laravel integration tests on PHP 8.2–8.5, checks syntax, validates Composer metadata and audits dependencies. It also checks installation into a separate plain-PHP consumer, including the Composer-installed executable. See [CONTRIBUTING.md](CONTRIBUTING.md) and [SECURITY.md](SECURITY.md).

The randomness tests catch regressions such as the legacy time-based collisions; they are not a statistical certification of a cryptographic primitive. Security relies on PHP's operating-system-backed random functions.

MIT © Farhad Arjmand. See [LICENSE.md](LICENSE.md).
