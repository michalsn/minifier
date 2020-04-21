<?php namespace Michalsn\Minifier\Adapters\Js;

use Michalsn\Minifier\Adapters\AdapterInterface;

class MinifyAdapter implements AdapterInterface
{
    /**
     * Adapter object
     *
     * @var object
     */
    protected $adapter;

    public function __construct()
    {
        $this->adapter = new \MatthiasMullie\Minify\JS();
    }

    public function add(string $file)
    {
        $this->adapter->add($file);
    }

    public function minify(string $file)
    {
        $this->adapter->minify($file);
    }

}