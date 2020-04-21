<?php namespace Michalsn\Minifier\Config;

use CodeIgniter\Config\BaseService;
use Michalsn\Minifier\Minifier;

class Services extends BaseService
{
    public static function minifier(bool $getShared = true)
    {
        if ($getShared)
        {
            return static::getSharedInstance('minifier');
        }

        $config = config('Minifier');

        return new Minifier($config);
    }
}