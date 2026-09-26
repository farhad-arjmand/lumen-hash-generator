#!/usr/bin/env bash
set -euo pipefail
package_dir="$(cd "$(dirname "$0")/.." && pwd)"
consumer_dir="$(mktemp -d)"
trap 'rm -rf "$consumer_dir"' EXIT
cd "$consumer_dir"
composer init --name=tests/token-consumer --no-interaction >/dev/null
composer config repositories.local path "$package_dir"
composer require 'farhad-arjmand/lumen-hash-generator:@dev' --no-interaction --prefer-dist >/dev/null
php -r 'require "vendor/autoload.php"; if (class_exists("Illuminate\\Support\\ServiceProvider")) { throw new RuntimeException("Unexpected runtime framework dependency"); } $g = new FarhadArjmand\LumenHashGenerator\TokenGenerator(); if (strlen($g->generate()) !== 32) { throw new RuntimeException("Invalid length"); }'
vendor/bin/hash-generator --format=hex --bytes=16 --count=2 --json > tokens.json
php -r '$tokens = json_decode(file_get_contents("tokens.json"), true, flags: JSON_THROW_ON_ERROR); if (count($tokens) !== 2 || !preg_match("/^[a-f0-9]{32}$/D", $tokens[0])) { throw new RuntimeException("Invalid CLI output"); }'
echo 'Plain-PHP consumer installation and Composer executable verified.'
