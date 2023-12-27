<?php

namespace Tests\Commands;

use CodeIgniter\Test\Filters\CITestStreamFilter;
use Michalsn\Minifier\Config\Minifier as MinifierConfig;
use Tests\Support\CLITestCase;

/**
 * @internal
 */
final class MinifyAllTest extends CLITestCase
{
    public function testRunWithNoProperConfig(): void
    {
        CITestStreamFilter::registration();
        CITestStreamFilter::addErrorFilter();

        $this->assertNotFalse(command('minify:all'));
        $output = $this->parseOutput(CITestStreamFilter::$buffer);

        CITestStreamFilter::removeErrorFilter();

        $this->assertSame('file_put_contents(./assets/versions.json): Failed to open stream: No such file or directory', $output);
    }

    public function testRun(): void
    {
        $config             = config(MinifierConfig::class);
        $config->dirJs      = SUPPORTPATH . 'assets/js';
        $config->dirCss     = SUPPORTPATH . 'assets/css';
        $config->dirMinJs   = SUPPORTPATH . 'assets/js';
        $config->dirMinCss  = SUPPORTPATH . 'assets/css';
        $config->dirVersion = SUPPORTPATH . 'assets';
        $config->js         = ['all.min.js' => ['bootstrap.js', 'jquery-3.7.1.js', 'main.js']];
        $config->css        = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']];

        service('minifier', $config);

        CITestStreamFilter::registration();
        CITestStreamFilter::addOutputFilter();

        $this->assertNotFalse(command('minify:all'));
        $this->parseOutput(CITestStreamFilter::$buffer);

        CITestStreamFilter::removeOutputFilter();

        $this->assertSame('All files were successfully generated.', $this->getLine(1));
    }
}
