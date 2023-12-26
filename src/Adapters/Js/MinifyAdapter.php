<?php

namespace Michalsn\Minifier\Adapters\Js;

use MatthiasMullie\Minify\JS;
use Michalsn\Minifier\Adapters\AdapterInterface;

class MinifyAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     */
    protected JS $adapter;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->adapter = new JS();
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
