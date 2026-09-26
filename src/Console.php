<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator;

use InvalidArgumentException;

final class Console
{
    public const HELP = <<<'HELP'
Usage: hash-generator [options]

  --format=alpha|hex|base64url  Output encoding (default: alpha)
  --length=32                 Random characters for alpha (at least 128-bit entropy)
  --alphabet=CHARS            Distinct printable ASCII characters for alpha
  --bytes=32                  Random bytes for hex/base64url (16–1024)
  --count=1                   Number of tokens (1–1000)
  --prefix=api_               Prefix outside the random part (max 64 characters)
  --json                      Emit a JSON array instead of one token per line
  --help                      Show this help

Examples:
  hash-generator --prefix=api_ --count=3
  hash-generator --format=base64url --bytes=32 --json

Tokens are secrets: do not capture stdout in shared logs.
HELP;

    /** @param list<string> $arguments */
    public function run(array $arguments): string
    {
        if ($arguments === ['--help']) {
            return self::HELP . "\n";
        }
        $options = [];
        foreach ($arguments as $argument) {
            if ($argument === '--json') {
                $key = 'json';
                $value = true;
            } elseif (preg_match('/\A--(format|length|alphabet|bytes|count|prefix)=(.*)\z/s', $argument, $matches)) {
                [, $key, $value] = $matches;
            } else {
                throw new InvalidArgumentException('Unknown argument. Run with --help for usage.');
            }
            if (array_key_exists($key, $options)) {
                throw new InvalidArgumentException('Duplicate option: --' . $key);
            }
            $options[$key] = $value;
        }
        $format = $options['format'] ?? 'alpha';
        if (!in_array($format, ['alpha', 'hex', 'base64url'], true)) {
            throw new InvalidArgumentException('Format must be alpha, hex or base64url.');
        }
        if (($format === 'alpha' && isset($options['bytes'])) || ($format !== 'alpha' && (isset($options['length']) || isset($options['alphabet'])))) {
            throw new InvalidArgumentException('Use length/alphabet only with alpha, or bytes only with hex/base64url.');
        }
        $count = $this->integer($options['count'] ?? '1');
        TokenGenerator::validateCount($count);
        $generator = new TokenGenerator();
        $prefix = $options['prefix'] ?? '';
        $tokens = [];
        for ($i = 0; $i < $count; $i++) {
            $tokens[] = match ($format) {
                'hex' => $generator->hex($this->integer($options['bytes'] ?? '32'), $prefix),
                'base64url' => $generator->base64Url($this->integer($options['bytes'] ?? '32'), $prefix),
                default => $generator->generate($this->integer($options['length'] ?? '32'), $options['alphabet'] ?? TokenGenerator::ALPHANUMERIC, $prefix),
            };
        }

        return (isset($options['json']) ? json_encode($tokens, JSON_THROW_ON_ERROR) : implode("\n", $tokens)) . "\n";
    }

    private function integer(string $value): int
    {
        if (!preg_match('/\A[1-9][0-9]{0,8}\z/', $value)) {
            throw new InvalidArgumentException('Numeric options must be positive integers.');
        }

        return (int) $value;
    }
}
