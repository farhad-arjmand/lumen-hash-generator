<?php

/**
 * Helper Functions
 *
 * @package  FarhadArjmand\LumenHashGenerator
 * @author   Themesfa <info@themesfa.net>
 */


/**
 * Monolog
 */
if (!function_exists('monolog')) {
	function monolog() {

		// Create the logger
		$logger      = new Monolog\Logger( 'lumen-hash-generator' );
		$logger_path = app()->storagePath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'hash.log';

		// Now add some handlers
		try {
			$logger->pushHandler( new Monolog\Handler\StreamHandler( $logger_path, Monolog\Logger::INFO ) );
		} catch ( Exception $e ) {
		}

		return $logger;
	}
}


/**
 * Brought by https://gist.github.com/mabasic/21d13eab12462e596120
 */
if (!function_exists('config_path')) {
	/**
	 * Get the configuration path.
	 *
	 * @param  string $path
	 * @return string
	 */
	function config_path($path = '') {
		return app()->basePath().DIRECTORY_SEPARATOR.'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}
}