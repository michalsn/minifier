<?php

if (! function_exists('minifier')) {
    /**
     * load asset file (js, css)
     *
     * @param string $filename Compressed asset filename
     */
    function minifier(string $filename): string
    {
        return service('minifier')->load($filename);
    }
}
