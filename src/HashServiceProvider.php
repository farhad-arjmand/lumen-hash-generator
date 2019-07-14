<?php namespace FarhadArjmand\LumenHashGenerator;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class     HashServiceProvider
 *
 * @package  Themesfa\LumenHashGenerator
 * @author   Themesfa <info@themesfa.net>
 */
class HashServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function boot() {
		# Fix: Specified key was too long error
		Schema::defaultStringLength(191);

		# Load Factories
		$this->app->make( 'Illuminate\Database\Eloquent\Factory' )->load( __DIR__ . '/../database/factories' );

		# Load Resources
		$this->loadRoutesFrom( __DIR__ . '/routes.php' );
		$this->loadMigrationsFrom( __DIR__ . '/../database/migrations' );

		# Load Config
		$this->publishes([
			$this->config_path('hash.php') => config_path('hash.php'),
		], 'config');

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		# Enables Facades
		app()->withFacades();
		// app()->withEloquent();

		# Register Config
		app()->configure('hash'); // tell lumen to load config/hash.php
		$this->mergeConfigFrom($this->config_path('hash.php'), 'hash');

		# Register Controller
		// $this->app->make( 'Themesfa\LumenHashGenerator\HashController' );
	}


	/**
	 * Get the package configuration path.
	 *
	 * @param  string $path
	 * @return string
	 */
	public function config_path($path = '') {
		return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . ( $path ? DIRECTORY_SEPARATOR . $path : $path );
	}
}