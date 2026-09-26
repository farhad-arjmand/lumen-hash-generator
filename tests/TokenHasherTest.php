<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator\Tests;

use FarhadArjmand\LumenHashGenerator\TokenHasher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TokenHasherTest extends TestCase
{
    public function testKnownSha256VectorAndTampering(): void
    {
        $hasher = new TokenHasher();
        $digest = 'ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad';
        self::assertSame($digest, $hasher->digest('abc'));
        self::assertTrue($hasher->verify('abc', $digest));
        self::assertFalse($hasher->verify('abd', $digest));
        self::assertFalse($hasher->verify('', $digest));
        self::assertFalse($hasher->verify('abc', 'invalid'));
        self::assertFalse($hasher->verify('abc', $digest . "\n"));
    }

    public function testHmacMatchesRfc4231VectorAndRequiresSameKey(): void
    {
        $hasher = new TokenHasher(str_repeat(chr(0xaa), 131));
        $token = 'Test Using Larger Than Block-Size Key - Hash Key First';
        $expected = '60e431591ee0b67f0d8a26aacbf5b77f8e0bc6213728c5140546040f0ee37f54';
        self::assertSame($expected, $hasher->digest($token));
        self::assertTrue($hasher->verify($token, $expected));
        self::assertFalse((new TokenHasher(str_repeat('b', 32)))->verify($token, $expected));
        self::assertFalse((new TokenHasher())->verify($token, $expected));
    }

    public function testEmptyTokensCannotBeStored(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new TokenHasher())->digest('');
    }

    public function testShortPepperRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TokenHasher(str_repeat('a', 31));
    }
}
