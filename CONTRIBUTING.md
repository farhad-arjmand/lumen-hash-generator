# Contributing

Use PHP 8.2+ and Composer 2. Install development dependencies with `composer install`.

```sh
composer validate --strict
composer lint
composer test
composer audit
php examples/basic.php
```

The core has no framework dependency. Keep the Laravel adapter thin. Do not add routes, migrations, token logging, global helpers, home-grown RNGs or silent compatibility fallbacks.

Changes to token formats, entropy limits, digest semantics or defaults need regression tests and migration notes. Generated secret values must never be committed as fixtures or copied from a production system. Fixed published test vectors are welcome.

Runtime dependencies are intentionally empty. Development dependencies float within supported ranges so CI can verify current framework versions; this library does not commit a Composer lock file. Applications should commit their own lock file.
