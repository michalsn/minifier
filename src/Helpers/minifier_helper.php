<?php

if (! function_exists('minifier')) {
    /**
     * Load asset file (js, css)
     *
     * @param string $filename Compressed asset filename
     */
    function minifier(string $filename): string
    {
        return service('minifier')->load($filename);
    }
}
