<?php namespace Michalsn\Minifier\Adapters;

interface AdapterInterface
{
	/**
	 * Add file
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function add(string $file);

	/**
	 * Minify file
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function minify(string $file);
}