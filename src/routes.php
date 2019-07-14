<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router = $this->app->router;

$router->group(['prefix' => 'hash'], function() use ($router)
{
	$router->group(['namespace' => 'FarhadArjmand\LumenHashGenerator\Controllers'], function() use ($router)
	{

		# example.com/hash/generator
		$router->group(['middleware' => 'FarhadArjmand\LumenHashGenerator\Middleware\JwtMiddleware'], function() use ($router)
		{
			$router->post('/generator',  [
				'as' => 'generator', 'uses' => 'HashController@generator'
			]);

		});

		# example.com/hash/auth
		$router->group(['prefix' => 'auth'], function() use ($router)
		{
			$router->post('/login',  [
				'as' => 'login', 'uses' => 'AuthController@login'
			]);

			$router->post('/register',  [
				'as' => 'register', 'uses' => 'AuthController@register'
			]);
		});
	});
});


