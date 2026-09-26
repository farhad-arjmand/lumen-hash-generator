<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator;

use InvalidArgumentException;

/** Digest high-entropy tokens for storage. This is NOT a password hasher. */
final class TokenHasher
{
    public function __construct(#[\SensitiveParameter] private readonly ?string $pepper = null)
    {
        if ($pepper !== null && strlen($pepper) < 32) {
            throw new InvalidArgumentException('An optional HMAC pepper must contain at least 32 bytes.');
        }
    }

    public function digest(#[\SensitiveParameter] string $token): string
    {
        if ($token === '') {
            throw new InvalidArgumentException('Cannot digest an empty token.');
        }

        return $this->pepper === null
            ? hash('sha256', $token)
            : hash_hmac('sha256', $token, $this->pepper);
    }

    public function verify(#[\SensitiveParameter] string $token, string $digest): bool
    {
        if ($token === '' || !preg_match('/\A[a-f0-9]{64}\z/', $digest)) {
            return false;
        }

        return hash_equals($digest, $this->digest($token));
    }
}
