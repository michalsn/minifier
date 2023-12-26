<?php

namespace Michalsn\Minifier\Adapters\Css;

use MatthiasMullie\Minify\CSS;
use Michalsn\Minifier\Adapters\AdapterInterface;

class MinifyAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     */
    protected CSS $adapter;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->adapter = new CSS();
    }

    /**
     * Add file
     *
     * @param string $file File name
     *
     * @return void;
     */
    public function add(string $file): void
    {
        $this->adapter->add($file);
    }

    /**
     * Minify file
     *
     * @param string $file File name
     *
     * @return void;
     */
    public function minify(string $file): void
    {
        $this->adapter->minify($file);
    }
}
