<?php namespace Michalsn\Minifier\Config;

use CodeIgniter\Config\BaseService;
use Michalsn\Minifier\Config\Minify;
use Michalsn\Minifier\Libraries\Minification;


class Services extends BaseService
{
    public static function minifier(BaseConfig $config = null, bool $getShared = true)
    {
		if ($getShared)
		{
			return static::getSharedInstance('minifier', $config);
		}

		if (empty($config))
		{
			$config = class_exists('\Config\Minifier') ? new \Config\Minifier() : new Minifier();
		}

		return new Minification($config);
	}
}