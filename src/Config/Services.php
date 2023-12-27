<?php

namespace Michalsn\Minifier\Config;

use CodeIgniter\Config\BaseService;
use Michalsn\Minifier\Config\Minifier as MinifierConfig;
use Michalsn\Minifier\Minifier;

class Services extends BaseService
{
    public static function minifier(?MinifierConfig $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('minifier', $config);
        }

        /** @var MinifierConfig $config */
        $config ??= config('Minifier');

        return new Minifier($config);
    }
}
