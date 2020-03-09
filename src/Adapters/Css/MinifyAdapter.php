<?php namespace Michalsn\Minifier\Css\Adapters;

use Michalsn\Minifier\AdapterInterface;
use MatthiasMullie\Minify;

class MinifyAdapter implements AdapterInterface
{
	/**
	 * Adapter object
	 *
	 * @var object
	 */
	protected $adapter;

	public function __construct()
	{
		$this->adapter = new CSS();
	}

	public function add(string $file)
	{
		$this->adapter->add($file);
	}

	public function minify(string $file)
	{
		$this->adapter->minify($file);
	}

}