<?php

namespace Michalsn\Minifier\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;
use Throwable;

class MinifyPublish extends BaseCommand
{
    protected $group       = 'Minifier';
    protected $name        = 'minify:publish';
    protected $description = 'Minify config file publisher.';

    /**
     * Copy config file
     */
    public function run(array $params): void
    {
        $source = service('autoloader')->getNamespace('Michalsn\\Minifier')[0];

        $publisher = new Publisher($source, APPPATH);

        try {
            $publisher->addPaths([
                'Config/Minifier.php',
            ])->merge(false);
        } catch (Throwable $e) {
            $this->showError($e);

            return;
        }

        foreach ($publisher->getPublished() as $file) {
            $contents = file_get_contents($file);
            $contents = str_replace('namespace Michalsn\\Minifier\\Config', 'namespace Config', $contents);
            $contents = str_replace('use CodeIgniter\\Config\\BaseConfig', 'use Michalsn\\Minifier\\Config\\Minifier as BaseMinifier', $contents);
            $contents = str_replace('class Minifier extends BaseConfig', 'class Minifier extends BaseMinifier', $contents);
            file_put_contents($file, $contents);
        }

        CLI::write(CLI::color('  Published! ', 'green') . 'You can customize the configuration by editing the "app/Config/Minifier.php" file.');
    }
}
