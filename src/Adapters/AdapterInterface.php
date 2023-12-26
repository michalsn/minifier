<?php

namespace Michalsn\Minifier\Adapters;

interface AdapterInterface
{
    /**
     * Add file
     *
     * @return void;
     */
    public function add(string $file): void;

    /**
     * Minify file
     *
     * @return void;
     */
    public function minify(string $file): void;
}
