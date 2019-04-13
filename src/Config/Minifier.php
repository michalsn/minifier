<?php namespace Michalsn\Minifier\Config;

use CodeIgniter\Config\BaseConfig;

class Minifier extends BaseConfig
{
	public $minify = true;

	public $baseUrl = null;

	public $dirJs = './assets/js';

	public $dirCss = './assets/css';

	public $dirVersion = './assets';

	public $tagJs = '<script type="text/javascript" src="%s"></script>';

	public $tagCss = '<link rel="stylesheet" href="%s">';

	public $js = [
		'all.min.js' => [
			'jquery-3.2.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
		],
	];

	public $css = [
		'all.min.css' => [
			'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
		],
	];
}