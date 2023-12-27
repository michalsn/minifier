<?php

namespace Tests\Commands;

use CodeIgniter\Test\Filters\CITestStreamFilter;
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
        $this->setMinifierConfig();

        CITestStreamFilter::registration();
        CITestStreamFilter::addOutputFilter();

        $this->assertNotFalse(command('minify:all'));
        $this->parseOutput(CITestStreamFilter::$buffer);

        CITestStreamFilter::removeOutputFilter();

        $this->assertSame('All files were successfully generated.', $this->getLine(1));
    }
}
