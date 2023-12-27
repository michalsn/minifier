<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

abstract class TestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $this->resetServices();

        parent::setUp();
    }
}
