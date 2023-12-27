<?php

namespace Tests\Commands;

use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tests\Support\CLITestCase;

/**
 * @internal
 */
final class MinifyPublishTest extends CLITestCase
{
    public function testRun(): void
    {
        CITestStreamFilter::registration();
        CITestStreamFilter::addOutputFilter();

        $this->assertNotFalse(command('minify:publish'));
        $output = $this->parseOutput(CITestStreamFilter::$buffer);

        CITestStreamFilter::removeOutputFilter();

        $this->assertSame('  Published! You can customize the configuration by editing the "app/Config/Minifier.php" file.', $output);
    }
}
