<?php

use Michalsn\Minifier\Minifier;

class MinifierTest extends \CodeIgniter\Test\CIUnitTestCase
{
	protected $config;

	protected $minifier;

	public function setUp(): void
	{
		parent::setUp();

		$this->config = new \Michalsn\Minifier\Config\Minifier();

		$this->config->baseUrl = 'http://localhost/';
		$this->config->dirJs = SUPPORTPATH . 'assets/js';
		$this->config->dirCss = SUPPORTPATH . 'assets/css';
		$this->config->dirVersion = SUPPORTPATH . 'assets';
		$this->config->js = ['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']];
		$this->config->css = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']];

		
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

	public function testConfig()
	{
		$this->assertTrue($this->config instanceof \Michalsn\Minifier\Config\Minifier);

		$this->assertEquals('<script type="text/javascript" src="%s"></script>', $this->config->tagJs);
		$this->assertEquals('<link rel="stylesheet" href="%s">', $this->config->tagCss);

		$this->assertEquals(['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']], $this->config->js);
		$this->assertEquals(['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']], $this->config->css);
	}

	public function testDeployJs()
	{
		$this->minifier = new Minifier($this->config);

		$result = $this->minifier->deploy('js');

		$this->assertTrue($result);
	}

	public function testDeployCss()
	{
		$this->minifier = new Minifier($this->config);

		$result = $this->minifier->deploy('css');

		$this->assertTrue($result);
	}

	public function testDeployAll()
	{
		$this->minifier = new Minifier($this->config);

		$result = $this->minifier->deploy('all');

		$this->assertTrue($result);
	}

	public function testLoadJs()
	{
		$this->minifier = new Minifier($this->config);

		$result = $this->minifier->load('all.min.js');

		$this->assertEquals('<script type="text/javascript" src="http://localhost' . SUPPORTPATH . 'assets/js/all.min.js?v=9ef881911da8d7c4a1c2f19c4878d122"></script>' . PHP_EOL, $result);
	}

	public function testLoadCssWithBaseCssUrl()
	{
		$this->config->baseCssUrl = 'http://css.localhost/';

		$this->minifier = new Minifier($this->config);

		$result = $this->minifier->load('all.min.css');

		$this->assertEquals('<link rel="stylesheet" href="http://css.localhost/all.min.css?v=50a35b0b1d1c3798aa556b8245314930">' . PHP_EOL, $result);
	}

}
