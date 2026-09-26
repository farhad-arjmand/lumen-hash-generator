<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator\Tests;

use FarhadArjmand\LumenHashGenerator\TokenGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TokenGeneratorTest extends TestCase
{
    public function testDefaultAndCustomAlphabetFormats(): void
    {
        $generator = new TokenGenerator();
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9]{32}\z/', $generator->generate());
        self::assertMatchesRegularExpression('/\Ainvite_[0123456789ABCDEFGHJKMNPQRSTVWXYZ]{26}\z/', $generator->generate(26, TokenGenerator::HUMAN_READABLE, 'invite_'));
        self::assertMatchesRegularExpression('/\A[01]{128}\z/', $generator->generate(128, '01'));
        self::assertSame(4096, strlen($generator->generate(4096)));
    }

    #[DataProvider('byteLengths')]
    public function testByteEncodingsRoundTrip(int $bytes): void
    {
        $generator = new TokenGenerator();
        $hex = $generator->hex($bytes, 'key_');
        self::assertSame(4 + $bytes * 2, strlen($hex));
        self::assertSame($bytes, strlen(hex2bin(substr($hex, 4))));
        $url = $generator->base64Url($bytes);
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9_-]+\z/', $url);
        self::assertSame((int) ceil($bytes * 8 / 6), strlen($url));
        self::assertSame($bytes, strlen(base64_decode(strtr($url, '-_', '+/'), true)));
    }

    public static function byteLengths(): array
    {
        return [[16], [17], [18], [32], [1024]];
    }

    public function testRapidBatchDoesNotRepeatTimeBasedTokens(): void
    {
        $tokens = (new TokenGenerator())->batch(256, prefix: 'api_');
        self::assertCount(256, $tokens);
        self::assertCount(256, array_unique($tokens));
        foreach ($tokens as $token) {
            self::assertMatchesRegularExpression('/\Aapi_[A-Za-z0-9]{32}\z/', $token);
        }
    }

    #[DataProvider('invalidArguments')]
    public function testRejectsUnsafeOrUnboundedArguments(string $method, array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new TokenGenerator())->$method(...$arguments);
    }

    public static function invalidArguments(): array
    {
        return [
            ['generate', [0]], ['generate', [-1]], ['generate', [4097]],
            ['generate', [16]], ['generate', [127, '01']],
            ['generate', [32, '']], ['generate', [128, 'a']],
            ['generate', [128, 'aab']], ['generate', [128, 'ab ']],
            ['generate', [128, "ab\n"]], ['generate', [128, 'آب']],
            ['generate', [32, TokenGenerator::ALPHANUMERIC, str_repeat('a', 65)]],
            ['generate', [32, TokenGenerator::ALPHANUMERIC, "bad\n"]],
            ['hex', [15]], ['hex', [1025]], ['hex', [16, 'bad/']],
            ['base64Url', [15]], ['base64Url', [1025]], ['base64Url', [16, 'bad+']],
            ['batch', [0]], ['batch', [-1]], ['batch', [1001]],
        ];
    }
}
