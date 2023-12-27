<?php

namespace Tests\Support;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Test\ReflectionHelper;
use Michalsn\Minifier\Config\Minifier as MinifierConfig;

abstract class CLITestCase extends TestCase
{
    use ReflectionHelper;

    private array $lines = [];

    protected function parseOutput(string $output): string
    {
        $this->lines = [];
        $output      = $this->removeColorCodes($output);
        $this->lines = explode("\n", $output);

        return $output;
    }

    protected function getLine(int $line = 0): ?string
    {
        return $this->lines[$line] ?? null;
    }

    protected function getLines(): string
    {
        return implode('', $this->lines);
    }

    protected function removeColorCodes(string $output): string
    {
        $colors = $this->getPrivateProperty(CLI::class, 'foreground_colors');
        $colors = array_values(array_map(static fn ($color) => "\033[" . $color . 'm', $colors));
        $colors = ["\033[0m", ...$colors];

        $output = str_replace($colors, '', trim($output));

        if (is_windows()) {
            $output = str_replace("\r\n", "\n", $output);
        }

        return $output;
    }

    protected function setMinifierConfig()
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
    }
}
