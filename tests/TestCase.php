<?php namespace FarhadArjmand\LumenHashGenerator\Tests;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  FarhadArjmand\LumenHashGenerator\Tests
 * @author   Themesfa <info@themesfa.net>
 */
abstract class TestCase extends BaseTestCase
{
	/* -----------------------------------------------------------------
	 |  Main Methods
	 | -----------------------------------------------------------------
	 */
	/**
	 * Setup the test environment.
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->loadMigrationsFrom(__DIR__ .'/../database/migrations');
	}
	/**
	 * Get package providers.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 *
	 * @return array
	 */
	protected function getPackageProviders($app)
	{
		return [
			\FarhadArjmand\LumenHashGenerator\HashServiceProvider::class,
		];
	}
	/**
	 * Resolve application HTTP Kernel implementation.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 */
	protected function resolveApplicationHttpKernel($app)
	{
		$app->singleton(\Illuminate\Contracts\Http\Kernel::class, Stubs\Http\Kernel::class);
	}
	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application   $app
	 */
	protected function getEnvironmentSetUp($app)
	{
		//
	}
}