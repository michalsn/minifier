<?php namespace Michalsn\Minifier\Adapters\Css;

use Michalsn\Minifier\Adapters\AdapterInterface;

class MinifyAdapter implements AdapterInterface
{
    /**
     * Adapter object.
     *
     * @var object
     */
    protected $adapter;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->adapter = new \MatthiasMullie\Minify\CSS();
    }

    /**
     * Add file
     *
     * @param string $file File name
     *
     * @return void;
     */
    public function add(string $file)
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
    public function minify(string $file)
    {
        $this->adapter->minify($file);
    }

}
