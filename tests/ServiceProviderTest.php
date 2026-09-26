<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator\Tests;

use FarhadArjmand\LumenHashGenerator\HashServiceProvider;
use FarhadArjmand\LumenHashGenerator\TokenGenerator;
use FarhadArjmand\LumenHashGenerator\TokenHasher;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;

final class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [HashServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('hash-generator.pepper', null);
    }

    public function testContainerBindingsAndDefaultConfig(): void
    {
        self::assertSame($this->app->make(TokenGenerator::class), $this->app->make(TokenGenerator::class));
        self::assertSame(hash('sha256', 'example'), $this->app->make(TokenHasher::class)->digest('example'));
        self::assertMatchesRegularExpression('/\A[A-Za-z0-9]{32}\z/', $this->app->make(TokenGenerator::class)->generate());
    }

    public function testApplicationPepperIsHonored(): void
    {
        $pepper = str_repeat('k', 32);
        $this->app['config']->set('hash-generator.pepper', $pepper);
        self::assertSame(hash_hmac('sha256', 'token', $pepper), $this->app->make(TokenHasher::class)->digest('token'));
    }

    public function testConfigPublicationIsNamespacedAndNoLegacyRoutesExist(): void
    {
        self::assertContains($this->app->configPath('hash-generator.php'), ServiceProvider::pathsToPublish(HashServiceProvider::class, 'hash-generator-config'));
        self::assertSame('bcrypt', $this->app['config']->get('hashing.driver'));
        $this->postJson('/hash/generator')->assertNotFound();
        $this->postJson('/hash/auth/register')->assertNotFound();
        $this->postJson('/hash/auth/login')->assertNotFound();
    }

    public function testConfigCanBeCached(): void
    {
        try {
            $this->artisan('config:cache')->assertExitCode(0)->run();
            self::assertFileExists($this->app->getCachedConfigPath());
            $config = require $this->app->getCachedConfigPath();
            self::assertArrayHasKey('hash-generator', $config);
        } finally {
            $this->artisan('config:clear')->assertExitCode(0)->run();
        }
    }
}
