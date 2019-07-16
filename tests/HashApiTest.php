<?php namespace FarhadArjmand\LumenHashGenerator\Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Class     HashApiTest
 *
 * @package  FarhadArjmand\LumenHashGenerator\Tests
 * @author   Themesfa <info@themesfa.net>
 */
class HashApiTest extends TestCase
{
	use DatabaseMigrations,DatabaseTransactions;

	private $token = 'test';

	/** @test */
	public function RegisterUser()
	{
		$parameters = [ 'email'    => 'test@gmail.com',
						'name'     => 'John Dou',
						'country'  => 'USA',
						'password' => '12345', ];

		$this->json( 'POST', "hash/auth/register", $parameters )
			 ->log('register')
			 ->seeStatusCode(200)
			 ->seeJsonStructure(['token']);
	}

	/** @test */
	public function LoginUser()
	{
		# 1. Register user.
		$parameters = [ 'email'    => 'test@gmail.com',
						'name'     => 'John Dou',
						'country'  => 'USA',
						'password' => '12345', ];

		$this->json( 'POST', "hash/auth/register", $parameters );

		# 2. Test Login
		$parameters = [ 'email'    => 'test@gmail.com',
						'password' => '12345' ];

		$this->json( 'POST', "hash/auth/login", $parameters )
			 ->log('login')
			 ->setToken()
			 ->seeStatusCode(200)
		     ->seeJsonStructure(['token']);
	}

	/** @test */
	public function GenerateHash()
	{
		# 1. Register user.
		$parameters = [ 'email'    => 'test@gmail.com',
						'name'     => 'John Dou',
						'country'  => 'USA',
						'password' => '12345', ];

		$this->json( 'POST', "hash/auth/register", $parameters );

		# 2. Login & Get Token
		$parameters = [ 'email'    => 'test@gmail.com',
						'password' => '12345' ];

		$this->json( 'POST', "hash/auth/login", $parameters )
			 ->setToken();

		# 3. Test Generate Hash
		$parameters = [ 'token' => $this->token ];

		$this->json( 'POST', '/hash/generator', $parameters )
			 ->log('hash')
			 ->seeStatusCode(200)
			 ->seeJsonStructure(['hash']);
	}

	private function setToken(){
		if ( isset( $this->response->original['token'] ) ) {
			$this->token = $this->response->original['token'];
		}

		return $this;
	}

}