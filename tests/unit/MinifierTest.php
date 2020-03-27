<?php

use Michalsn\Minifier\Minifier;

class MinifierTest extends \CodeIgniter\Test\CIUnitTestCase
{
	protected $minifier;

	public function setUp(): void
	{
		parent::setUp();

		$config = new \Michalsn\Minifier\Config\Minifier();

		$config->baseUrl = 'http://localhost/';
		$config->dirJs = SUPPORTPATH . 'assets/js';
		$config->dirCss = SUPPORTPATH . 'assets/css';
		$config->dirVersion = SUPPORTPATH . 'assets';
		$config->js = ['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']];
		$config->css = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']];

		$this->minifier = new Minifier($config);
/*
		if (file_exists($config->dirJs . '/all.min.js'))
		{
			unlink($config->dirJs . '/all.min.js');
		}

		if (file_exists($config->dirCss . '/all.min.css'))
		{
			unlink($config->dirCss . '/all.min.css');
		}

		if (file_exists($config->dirVersion . '/versions.js'))
		{
			unlink($config->dirVersion . 'versions.js');
		}
*/
	}

	public function testDeployJs()
	{
		$result = $this->minifier->deploy('js');

		$this->assertTrue($result);
	}

	public function testDeployCss()
	{
		$result = $this->minifier->deploy('css');

		$this->assertTrue($result);
	}

	public function testDeployAll()
	{
		$result = $this->minifier->deploy('all');

		$this->assertTrue($result);
	}

	public function testLoadJs()
	{
		$result = $this->minifier->load('all.min.js');

		$this->assertEquals('<script type="text/javascript" src="http://localhost' . SUPPORTPATH . 'assets/js/all.min.js?v=9ef881911da8d7c4a1c2f19c4878d122"></script>' . PHP_EOL, $result);
	}

	public function testLoadCss()
	{
		$this->minifier->deploy('css');
		$result = $this->minifier->load('all.min.css');

		$this->assertEquals('<link rel="stylesheet" href="http://localhost' . SUPPORTPATH . 'assets/css/all.min.css?v=50a35b0b1d1c3798aa556b8245314930">' . PHP_EOL, $result);
	}
}
