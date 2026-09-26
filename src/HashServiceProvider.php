<?php

declare(strict_types=1);

namespace FarhadArjmand\LumenHashGenerator;

use Illuminate\Support\ServiceProvider;

/** Optional Laravel adapter. Does not register routes, migrations, auth or helpers. */
final class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hash-generator.php', 'hash-generator');
        $this->app->singleton(TokenGenerator::class);
        $this->app->singleton(TokenHasher::class, fn ($app) => new TokenHasher($app['config']->get('hash-generator.pepper')));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/hash-generator.php' => $this->app->configPath('hash-generator.php'),
            ], 'hash-generator-config');
        }
    }
}
