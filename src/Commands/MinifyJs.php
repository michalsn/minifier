<?php

namespace Michalsn\Minifier\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\Services;

class MinifyJs extends BaseCommand
{
    protected $group       = 'Minifier';
    protected $name        = 'minify:js';
    protected $description = 'Minify JS assets.';

    /**
     * Prepare assets to use on website
     */
    public function run(array $params): void
    {
        $benchmark = Services::timer();

        $benchmark->start('minifier');

        $minify = service('minifier');
        $result = $minify->deploy('js');

        $benchmark->stop('minifier');

        if (! $result) {
            CLI::error($minify->getError());

            return;
        }

        $time = $benchmark->getElapsedTime('minifier');

        CLI::write('Finished in: ' . $time . 's.');
        CLI::write('JS files were successfully generated.', 'green');
    }
}
