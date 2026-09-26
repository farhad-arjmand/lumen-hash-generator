<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator\Tests;

use FarhadArjmand\LumenHashGenerator\Console;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class ConsoleTest extends TestCase
{
    public function testHelpDoesNotGenerateTokens(): void
    {
        self::assertStringContainsString('Usage:', (new Console())->run(['--help']));
    }

    public function testJsonBatchAndFormats(): void
    {
        $console = new Console();
        $tokens = json_decode($console->run(['--count=3', '--format=hex', '--bytes=16', '--prefix=api_', '--json']), true, flags: JSON_THROW_ON_ERROR);
        self::assertCount(3, $tokens);
        foreach ($tokens as $token) {
            self::assertMatchesRegularExpression('/\Aapi_[a-f0-9]{32}\z/', $token);
        }
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9_-]{43}\n\z/', $console->run(['--format=base64url']));
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9]{32}\n\z/', $console->run([]));
    }

    #[DataProvider('badOptions')]
    public function testInvalidOptionsFailBeforeOutput(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Console())->run($arguments);
    }

    public static function badOptions(): array
    {
        return [[['--unknown=secret']], [['--json', '--json']], [['--count=0']], [['--count=1001']], [['--count=1.5']], [['--count=1e3']], [['--count=999999999999999999999999']], [['--count=-2']], [['--format=md5']], [['--bytes=32']], [['--format=hex', '--length=32']], [['--format=base64url', '--alphabet=abc']], [['--length=8']], [['--help', '--count=2']]];
    }

    public function testExecutableExitCodesAndStreams(): void
    {
        $process = proc_open([PHP_BINARY, __DIR__ . '/../bin/hash-generator', '--format=invalid'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        self::assertSame('', stream_get_contents($pipes[1]));
        self::assertStringContainsString('Format must', stream_get_contents($pipes[2]));
        fclose($pipes[1]); fclose($pipes[2]);
        self::assertSame(2, proc_close($process));
    }

    public function testEntropyFailurePropagatesWithoutFallback(): void
    {
        $code = 'namespace FarhadArjmand\\LumenHashGenerator; function random_bytes(int $n): string { throw new \\RuntimeException("entropy unavailable"); } require ' . var_export(__DIR__ . '/../vendor/autoload.php', true) . '; try { (new TokenGenerator())->hex(); exit(1); } catch (\\RuntimeException $e) { exit($e->getMessage() === "entropy unavailable" ? 0 : 2); }';
        $process = proc_open([PHP_BINARY, '-r', $code], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        self::assertSame('', stream_get_contents($pipes[1]));
        self::assertSame('', stream_get_contents($pipes[2]));
        fclose($pipes[1]); fclose($pipes[2]);
        self::assertSame(0, proc_close($process));
    }
}
