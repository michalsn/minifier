<?php namespace Michalsn\Minifier\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\Config\Services;

class MinifyCss extends BaseCommand
{
    protected $group       = 'Minifier';
    protected $name        = 'minify:css';
    protected $description = 'Minify CSS assets.';

    //--------------------------------------------------------------------

    /**
     * Prepare assets to use on website
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $benchmark = Services::timer();

        $benchmark->start('minifier');

        $minify = Services::minifier();
        $result = $minify->deploy('css');

        $benchmark->stop('minifier');

        if (! $result) 
        {
            CLI::error($minify->getError());
            exit;
        }

        $time = $benchmark->getElapsedTime('minifier');

        CLI::write('Finished in: ' . $time . 's.'); 
        CLI::write('CSS files were successfully generated.', 'green');      
    }

    //--------------------------------------------------------------------

}