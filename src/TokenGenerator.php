<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator;

use InvalidArgumentException;

/** Stateless CSPRNG-backed generator. No persistence, logging or HTTP side effects. */
final class TokenGenerator
{
    public const ALPHANUMERIC = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    public const URL_SAFE = self::ALPHANUMERIC . '-_';
    public const HUMAN_READABLE = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    public const MAX_LENGTH = 4096;
    public const MAX_BYTES = 1024;
    public const MAX_BATCH = 1000;

    public function generate(int $length = 32, string $alphabet = self::ALPHANUMERIC, string $prefix = ''): string
    {
        $size = strlen($alphabet);
        if ($size < 2 || $size > 94 || preg_match('/[^!-~]/', $alphabet) || count(array_unique(str_split($alphabet))) !== $size) {
            throw new InvalidArgumentException('Alphabet must contain 2–94 distinct printable ASCII characters without spaces.');
        }
        if ($length < 1 || $length > self::MAX_LENGTH || $length * log($size, 2) < 128) {
            throw new InvalidArgumentException('Length must provide at least 128 bits of entropy and be at most 4096 characters.');
        }
        $this->validatePrefix($prefix);
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            // random_int avoids modulo bias even when the alphabet size is not a power of two.
            $token .= $alphabet[random_int(0, $size - 1)];
        }

        return $prefix . $token;
    }

    public function hex(int $bytes = 32, string $prefix = ''): string
    {
        $this->validateBytes($bytes);
        $this->validatePrefix($prefix);

        return $prefix . bin2hex(random_bytes($bytes));
    }

    public function base64Url(int $bytes = 32, string $prefix = ''): string
    {
        $this->validateBytes($bytes);
        $this->validatePrefix($prefix);

        return $prefix . rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    /** @return list<string> */
    public function batch(int $count, int $length = 32, string $alphabet = self::ALPHANUMERIC, string $prefix = ''): array
    {
        self::validateCount($count);
        $tokens = [];
        for ($i = 0; $i < $count; $i++) {
            $tokens[] = $this->generate($length, $alphabet, $prefix);
        }

        return $tokens;
    }

    public static function validateCount(int $count): void
    {
        if ($count < 1 || $count > self::MAX_BATCH) {
            throw new InvalidArgumentException('Count must be between 1 and 1000.');
        }
    }

    private function validateBytes(int $bytes): void
    {
        if ($bytes < 16 || $bytes > self::MAX_BYTES) {
            throw new InvalidArgumentException('Bytes must be between 16 and 1024 (at least 128 bits of entropy).');
        }
    }

    private function validatePrefix(string $prefix): void
    {
        if (!preg_match('/\A[A-Za-z0-9_-]{0,64}\z/', $prefix)) {
            throw new InvalidArgumentException('Prefix must be at most 64 ASCII letters, digits, underscores or hyphens.');
        }
    }
}
