# Migrating from the legacy package to the v2 rewrite

This is a breaking rewrite. Do not replace the old package in a production application without an application-level migration and rollback plan. Install the v2 series explicitly with the `^2.0` Composer constraint.

## Removed behavior

| Legacy | Replacement |
|---|---|
| `hash(salt . time())` | `TokenGenerator`, backed by the OS CSPRNG |
| POST `/hash/generator` | Call the generator in your own authenticated controller/service |
| `/hash/auth/login` and `/hash/auth/register` | Your application's authentication system |
| Bundled JWT middleware and `JWT_SECRET` | Your existing guard/Sanctum/Passport; no JWT implementation here |
| Bundled users-table migration | Application-owned schema; the new provider runs no migrations |
| `config/hash.php`: algo/raw/salt/log/jwt-leeway | `config/hash-generator.php`: optional pepper |
| Logging generated tokens and salts | No token logging |
| Global `monolog()` and `config_path()` helpers | No global helpers |

## Application migration

1. Inventory every consumer of the old routes and responses. Keep the old version pinned while updating clients. The new package does not emulate the old endpoints.
2. Move account creation and authentication into the host app before removing the old package's controllers. Never keep two competing users-table migrations.
3. Decide whether old generated values acted as credentials. Values derived from timestamps and static salts are predictable and repeat within one second. Revoke and reissue affected credentials rather than treating them as secure random tokens.
4. Leave existing application data in place. Removing this package does not justify dropping the users table. Do not run a migration rollback that deletes real user accounts.
5. Replace generation calls and token storage with the new API. Store only the digest plus application-owned expiry/scope/revocation data. Do not convert password storage to `TokenHasher`.
6. Remove the old provider registration, JWT middleware references and global helper calls. In Laravel, use package discovery (or explicitly register the new provider if discovery is disabled).
7. Remove only unused package-specific settings after checking other consumers. Never delete an environment key still used by another component. Publish the new config if needed, clear/rebuild config caches, and restart long-lived workers.
8. Exercise authorization, token expiry, revocation and replay protection in application tests before switching traffic. The package's unit tests cannot validate your application policy.

## Compatibility

- PHP minimum rises from 7.1 to 8.2.
- Modern Laravel integration is optional; legacy Laravel 5 and Lumen route/auth integration are removed.
- No `generateHash()`/controller compatibility shim is provided: silently preserving the old API would conceal a significant security and authentication change.
