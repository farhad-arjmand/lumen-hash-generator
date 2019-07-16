<?php namespace FarhadArjmand\LumenHashGenerator\Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  FarhadArjmand\LumenHashGenerator\Tests
 * @author   Themesfa <info@themesfa.net>
 */
abstract class TestCase extends BaseTestCase
{

	/**
	 * Creates the application.
	 *
	 * @return \Laravel\Lumen\Application
	 */
	public function createApplication()
	{
		return require __DIR__.'/../bootstrap/app.php';
	}

	/**
	 * Create a log.html file for debug results.
	 *
	 * @param string $name
	 * @return TestCase
	 */
	public function log($name = ''){

		if ( !empty( $name ) ) {
			$name = "log.$name";
		} else {
			$name = 'log';
		}

		file_put_contents( __DIR__ . "/{$name}.html", $this->response->getContent() );

		return $this;
	}
}