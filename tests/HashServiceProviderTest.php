<?php namespace FarhadArjmand\LumenHashGenerator\Tests;
/**
 * Class     HashServiceProviderTest
 *
 * @package  FarhadArjmand\LumenHashGenerator\Tests
 * @author   Themesfa <info@themesfa.net>
 */
class HashServiceProviderTest extends TestCase
{
	/* -----------------------------------------------------------------
	 |  Properties
	 | -----------------------------------------------------------------
	 */
	/** @var  \FarhadArjmand\LumenHashGenerator\HashServiceProvider */
	private $provider;
	/* -----------------------------------------------------------------
	 |  Main Methods
	 | -----------------------------------------------------------------
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->provider = $this->app->getProvider(\FarhadArjmand\LumenHashGenerator\HashServiceProvider::class);
	}
	protected function tearDown(): void
	{
		unset($this->provider);
		parent::tearDown();
	}
	/** @test */
	public function it_can_be_instantiated()
	{
		$expectations = [
			\Illuminate\Support\ServiceProvider::class,
			\FarhadArjmand\LumenHashGenerator\HashServiceProvider::class
		];
		foreach ($expectations as $expected) {
			static::assertInstanceOf($expected, $this->provider);
		}
	}
}