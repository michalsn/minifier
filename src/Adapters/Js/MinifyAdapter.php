<?php namespace Michalsn\Minifier\Js\Adapters;

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
		$this->adapter = new JS();
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