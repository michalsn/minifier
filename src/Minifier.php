<?php

namespace Michalsn\Minifier;

use Exception;
use Michalsn\Minifier\Config\Minifier as MinifierConfig;
use Michalsn\Minifier\Exceptions\MinifierException;

class Minifier
{
    /**
     * Error string.
     */
    protected string $error = '';

    /**
     * Prepare config to use
     */
    public function __construct(protected MinifierConfig $config)
    {
    }

    /**
     * Load minified file
     *
     * @param string $filename File name
     */
    public function load(string $filename): array|string
    {
        // determine file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (! in_array($ext, ['js', 'css'], true)) {
            throw MinifierException::forWrongFileExtension($ext);
        }

        if (! in_array($this->config->returnType, ['html', 'array', 'json'], true)) {
            throw MinifierException::forWrongReturnType($this->config->returnType);
        }

        if ($this->config->autoDeployOnChange) {
            $this->autoDeployCheckFile($ext, $filename);
        }

        // load versions
        $versions = $this->getVersion($this->config->dirVersion);

        $filenames = [];

        // do we use combined+minified+versioned assets?
        if ($this->config->minify) {
            if (isset($versions[$ext][$filename])) {
                $filenames[] = $filename . '?v=' . $versions[$ext][$filename];
            }
        } else {
            // load all files from config array for this filename
            $type      = $this->config->{$ext};
            $filenames = $type[$filename];
        }

        // determine tag template for file
        $tag = ($ext === 'js') ? $this->config->tagJs : $this->config->tagCss;

        // determine base URL address
        $dir = $this->determineUrl($ext);

        // prepare output
        return $this->prepareOutput($filenames, $dir, $tag);
    }

    /**
     * Deploy
     *
     * @param string $mode Deploy mode
     */
    public function deploy(string $mode = 'all'): bool
    {
        if (! in_array($mode, ['all', 'js', 'css'], true)) {
            throw MinifierException::forIncorrectDeploymentMode($mode);
        }

        try {
            $files = match ($mode) {
                'js'    => $this->deployFiles('js', $this->config->js, $this->config->dirJs, $this->config->dirMinJs),
                'css'   => $this->deployFiles('css', $this->config->css, $this->config->dirCss, $this->config->dirMinCss),
                default => [
                    'js'  => $this->deployFiles('js', $this->config->js, $this->config->dirJs, $this->config->dirMinJs),
                    'css' => $this->deployFiles('css', $this->config->css, $this->config->dirCss, $this->config->dirMinCss),
                ],
            };

            $this->setVersion($mode, $files, $this->config->dirVersion);

            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    /**
     * Return error
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Auto deploy check for CSS files
     *
     * @param string $fileType File type [css, js]
     * @param string $filename Filename
     */
    protected function autoDeployCheckFile(string $fileType, string $filename): bool
    {
        $dir    = 'dir' . ucfirst(strtolower($fileType));
        $dirMin = 'dirMin' . ucfirst(strtolower($fileType));

        if ($this->config->{$dirMin} === null) {
            $dirMin = $dir;
        }

        $assets   = [$filename => $this->config->{$fileType}[$filename]];
        $filePath = $this->config->{$dirMin} . DIRECTORY_SEPARATOR . $filename;

        // if file is not deployed
        if (! file_exists($filePath)) {
            $this->deployFiles($fileType, $assets, $this->config->{$dir}, $this->config->{$dirMin});

            return true;
        }

        // get last deploy time
        $lastDeployTime = filemtime($filePath);

        // loop though the files and check last update time
        foreach ($assets[$filename] as $file) {
            $currentFileTime = filemtime($this->config->{$dir} . DIRECTORY_SEPARATOR . $file);
            if ($currentFileTime > $lastDeployTime) {
                $this->deployFiles($fileType, $assets, $this->config->{$dir}, $this->config->{$dirMin});

                return true;
            }
        }

        return false;
    }

    /**
     * Determine URL address for asset
     *
     * @param string $ext Extension type
     */
    protected function determineUrl(string $ext): string
    {
        if ($ext === 'js' && $this->config->baseJsUrl !== null) {
            return rtrim($this->config->baseJsUrl, '/');
        }

        if ($ext === 'css' && $this->config->baseCssUrl !== null) {
            return rtrim($this->config->baseCssUrl, '/');
        }

        // determine file folder
        $dir = ($ext === 'js') ? $this->config->dirMinJs : $this->config->dirMinCss;
        $dir = ltrim(trim($dir, '/'), './');

        // add base url if needed
        if ($this->config->baseUrl !== null) {
            $dir = rtrim($this->config->baseUrl, '/') . '/' . $dir;
        }

        return $dir;
    }

    /**
     * Prepare output to return a desired format
     *
     * @param array  $filenames Filenames to return
     * @param string $dir       Directory
     * @param string $tag       HTML tag
     */
    protected function prepareOutput(array $filenames, string $dir, string $tag): array|string
    {
        // prepare output
        $output = '';

        foreach ($filenames as &$file) {
            if ($this->config->returnType === 'html') {
                $output .= sprintf($tag, $dir . '/' . $file) . PHP_EOL;
            } else {
                $file = $dir . '/' . $file;
            }
        }

        if ($this->config->returnType === 'html') {
            return $output;
        }

        if ($this->config->returnType === 'json') {
            return json_encode($filenames);
        }

        return $filenames;
    }

    /**
     * Load version file
     *
     * @param string $dir Directory
     */
    protected function getVersion(string $dir): array
    {
        static $versions = null;

        // load all versions numbers
        if ($versions === null) {
            $dir = rtrim($dir, DIRECTORY_SEPARATOR);

            if (! file_exists($dir . DIRECTORY_SEPARATOR . 'versions.json')) {
                throw MinifierException::forNoVersioningFile();
            }

            $versions = json_decode(file_get_contents($dir . DIRECTORY_SEPARATOR . 'versions.json'), true);
        }

        return $versions;
    }

    /**
     * Set version
     *
     * @param string $mode  Mode
     * @param array  $files Files
     * @param string $dir   Directory
     */
    protected function setVersion(string $mode, array $files, string $dir): void
    {
        $versions = [];
        $dir      = rtrim($dir, DIRECTORY_SEPARATOR);

        if (file_exists($dir . DIRECTORY_SEPARATOR . 'versions.json')) {
            $versions = json_decode(file_get_contents($dir . DIRECTORY_SEPARATOR . 'versions.json'), true);
        }

        if ($mode === 'all') {
            $versions = $files;
        } else {
            $versions[$mode] = $files;
        }

        file_put_contents($dir . DIRECTORY_SEPARATOR . 'versions.json', json_encode($versions));
    }

    /**
     * Deploy files
     *
     * @param string      $fileType File type [css, js]
     * @param array       $assets   CSS assets
     * @param string      $dir      Directory
     * @param string|null $minDir   Minified directory
     */
    protected function deployFiles(string $fileType, array $assets, string $dir, ?string $minDir = null): array
    {
        $adapterType = 'adapter' . ucfirst(strtolower($fileType));

        $dir = rtrim($dir, '/');

        if ($minDir === null) {
            $minDir = $dir;
        }

        $class = $this->config->{$adapterType};

        $results = [];

        foreach ($assets as $asset => $files) {
            $minClass = new $class();

            foreach ($files as $file) {
                if ($this->config->minify) {
                    $minClass->add($dir . DIRECTORY_SEPARATOR . $file);
                } elseif ($dir !== $minDir) {
                    $this->copyFile($dir . DIRECTORY_SEPARATOR . $file, $minDir . DIRECTORY_SEPARATOR . $file);
                    $results[$file] = md5_file($minDir . DIRECTORY_SEPARATOR . $file);
                }
            }

            if ($this->config->minify) {
                $minClass->minify($minDir . DIRECTORY_SEPARATOR . $asset);
                $results[$asset] = md5_file($minDir . DIRECTORY_SEPARATOR . $asset);
            }
        }

        return $results;
    }

    /**
     * Copy File
     *
     * @param string $dir    Directory
     * @param string $minDir Minified directory
     */
    protected function copyFile(string $dir, string $minDir): void
    {
        $path = pathinfo($minDir);

        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0755, true);
        }

        if (! copy($dir, $minDir)) {
            throw MinifierException::forFileCopyError($dir, $minDir);
        }
    }
}
