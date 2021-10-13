<?php namespace Michalsn\Minifier;

use Michalsn\Minifier\Exceptions\MinifierException;

class Minifier
{
    /**
     * Config object.
     *
     * @var \Config\Minify
     */
    protected $config;

    /**
     * Error string.
     *
     * @var string
     */
    protected $error = '';

    //--------------------------------------------------------------------

    /**
     * Prepare config to use
     *
     * @param \Config\Minify $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        // make some checks for backward compatibility
        // just in case someone doesn't publish/update
        // their configuration file
        if (! isset($this->config->baseJsUrl))
        {
            $this->config->baseJsUrl = null;
        }

        if (! isset($this->config->baseCssUrl))
        {
            $this->config->baseCssUrl = null;
        }

        if (! isset($this->config->returnType))
        {
            $this->config->returnType = 'html';
        }
    }

    //--------------------------------------------------------------------

    /**
     * Load minified file
     *
     * @param string $filename File name
     *
     * @return string|array
     */
    public function load(string $filename)
    {
        // determine file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (! in_array($ext, ['js', 'css']))
        {
            throw MinifierException::forWrongFileExtension($ext);
        }

        if (! in_array($this->config->returnType, ['html', 'array', 'json']))
        {
            throw MinifierException::forWrongReturnType($this->config->returnType);
        }

        if ($this->config->autoDeployOnChange)
        {
            $this->autoDeployCheck($filename, $ext);
        }

        // load versions
        $versions = $this->getVersion($this->config->dirVersion);

        $filenames = [];

        // do we use combined+minified+versioned assets?
        if ($this->config->minify)
        {
            if (isset($versions[$ext][$filename]))
            {
                $filenames[] = $filename . '?v=' . $versions[$ext][$filename];
            }
        }
        else
        {
            // load all files from config array for this filename
            $type      = $this->config->$ext;
            $filenames = $type[$filename];
        }

        // determine tag template for file
        $tag = ($ext === 'js') ?  $this->config->tagJs : $this->config->tagCss;

        // determine base URL address
        $dir = $this->determineUrl($ext);

        // prepare output
        return $this->prepareOutput($filenames, $dir, $tag);
    }

    //--------------------------------------------------------------------

    /**
     * Deploy
     *
     * @param string $mode Deploy mode
     *
     * @return bool
     */
    public function deploy(string $mode = 'all'): bool
    {
        if ( ! in_array($mode, ['all', 'js', 'css']))
        {
            throw MinifierException::forIncorrectDeploymentMode($mode);
        }

        $files = [];

        try
        {
            switch ($mode)
            {
                case 'js':
                    $files = $this->deployJs($this->config->js, $this->config->dirJs, $this->config->dirMinJs);
                    break;
                case 'css':
                    $files = $this->deployCss($this->config->css, $this->config->dirCss, $this->config->dirMinCss);
                    break;
                default:
                    $files['js']  = $this->deployJs($this->config->js, $this->config->dirJs, $this->config->dirMinJs);
                    $files['css'] = $this->deployCss($this->config->css, $this->config->dirCss, $this->config->dirMinCss);
            }

            $this->setVersion($mode, $files, $this->config->dirVersion);

            return true;
        }
        catch (Exception $e)
        {
            $this->error = $e->getMessage();
            return false;
        }


    }

    //--------------------------------------------------------------------

    /**
     * Return error
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    //--------------------------------------------------------------------

    /**
     * Auto deploy check
     *
     * @param string $filename Filename
     * @param string $ext      File extension
     *
     * @return void
     */
    protected function autoDeployCheck(string $filename, string $ext): void
    {
        switch ($ext)
        {
            case 'js':
                $this->autoDeployCheckJs($filename);
                break;
            case 'css':
                $this->autoDeployCheckCss($filename);
                break;
        }
    }

    //--------------------------------------------------------------------

    /**
     * Auto deploy check for JS files
     *
     * @param string $filename Filename
     *
     * @return bool
     */
    protected function autoDeployCheckJs(string $filename): bool
    {
        $assets   = [$filename => $this->config->js[$filename]];
        $filePath = $this->config->dirJs . '/' . $filename;

        // if file is not deployed
        if (! file_exists($filePath))
        {
            $this->deployJs($assets, $this->config->dirJs, $this->config->dirMinJs);
            return true;
        }

        // get last deploy time
        $lastDeployTime = filemtime($filePath);

        // loop though the files and check last update time
        foreach ($assets[$filename] as $file)
        {
            $currentFileTime = filemtime($this->config->dirJs . '/' . $file);
            if ($currentFileTime > $lastDeployTime)
            {
                $this->deployJs($assets, $this->config->dirJs, $this->config->dirMinJs);
                return true;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Auto deploy check for CSS files
     *
     * @param string $filename Filename
     *
     * @return bool
     */
    protected function autoDeployCheckCss(string $filename): bool
    {
        $assets   = [$filename => $this->config->css[$filename]];
        $filePath = $this->config->dirCss . '/' . $filename;

        // if file is not deployed
        if (! file_exists($filePath))
        {
            $this->deployCss($assets, $this->config->dirCss, $this->config->dirMinCss);
            return true;
        }

        // get last deploy time
        $lastDeployTime = filemtime($filePath);

        // loop though the files and check last update time
        foreach ($assets[$filename] as $file)
        {
            $currentFileTime = filemtime($this->config->dirCss . '/' . $file);
            if ($currentFileTime > $lastDeployTime)
            {
                $this->deployCss($assets, $this->config->dirCss, $this->config->dirMinCss);
                return true;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * Determine URL address for asset
     *
     * @param string $ext Extension type
     *
     * @return string
     */
    protected function determineUrl(string $ext): string
    {
        if ($ext === 'js' && $this->config->baseJsUrl !== null)
        {
            return rtrim($this->config->baseJsUrl, '/');
        }

        if ($ext === 'css' && $this->config->baseCssUrl !== null)
        {
            return rtrim($this->config->baseCssUrl, '/');
        }

        // determine file folder
        $dir = ($ext === 'js') ? $this->config->dirMinJs : $this->config->dirMinCss;
        $dir = ltrim(trim($dir, '/'), './');

        // add base url if needed
        if ($this->config->baseUrl !== null)
        {
            $dir = rtrim($this->config->baseUrl, '/') . '/' . $dir;
        }

        return $dir;

    }

    //--------------------------------------------------------------------

    /**
     * Prepare output to return a desired format
     *
     * @param array  $filenames Filenames to return
     * @param string $dir       Directory
     * @param string $tag       HTML tag
     *
     * @return string|array
     */
    protected function prepareOutput(array $filenames, string $dir, string $tag)
    {
        // prepare output
        $output = '';

        foreach ($filenames as &$file)
        {
            if ($this->config->returnType === 'html')
            {
                $output .= sprintf($tag, $dir . '/' . $file) . PHP_EOL;
            }
            else
            {
                $file = $dir . '/' . $file;
            }
        }

        if ($this->config->returnType === 'html')
        {
            return $output;
        }

        if ($this->config->returnType === 'json')
        {
            return json_encode($filenames);
        }

        return $filenames;
    }

    //--------------------------------------------------------------------

    /**
     * Load version file
     *
     * @param string $dir Directory
     *
     * @return array
     */
    protected function getVersion(string $dir): array
    {
        static $versions = null;

        // load all versions numbers
        if ($versions === null)
        {
            $dir = rtrim($dir, '/');

            if (! file_exists($dir . '/versions.json'))
            {
                throw MinifierException::forNoVersioningFile();
            }

            $versions = json_decode(file_get_contents($dir . '/versions.json'), true);
        }

        return $versions;
    }

    //--------------------------------------------------------------------

    /**
     * Set version
     *
     * @return void
     */
    protected function setVersion($mode, $files, $dir): void
    {
        $dir = rtrim($dir, '/');

        if (file_exists($dir . '/versions.json'))
        {
            $versions = json_decode(file_get_contents($dir . '/versions.json'), true);
        }

        if ($mode === 'all')
        {
            $versions = $files;
        }
        else
        {
            $versions[$mode] = $files;
        }

        file_put_contents($dir . '/versions.json', json_encode($versions));
    }

    //--------------------------------------------------------------------

    /**
     * Deploy JS
     *
     * @param array  $assets JS assets
     * @param string $dir    Directory
     *
     * @return array
     */
    protected function deployJs(array $assets, string $dir, string $minDir): array
    {
        $dir = rtrim($dir, '/');

        $class = $this->config->adapterJs;

        $results = [];

        foreach ($assets as $asset => $files)
        {
            $miniJs = new $class();
            foreach ($files as $file)
            {
                $miniJs->add($dir . '/' . $file);
            }

            $miniJs->minify($minDir . '/' . $asset);

            $results[$asset] = md5_file($minDir . '/' . $asset);
        }

        return $results;
    }

    //--------------------------------------------------------------------

    /**
     * Deploy CSS
     *
     * @param array  $assets CSS assets
     * @param string $dir    Directory
     *
     * @return array
     */
    protected function deployCss(array $assets, string $dir, string $minDir): array
    {
        $dir = rtrim($dir, '/');

        $class = $this->config->adapterCss;

        $results = [];

        foreach ($assets as $asset => $files)
        {
            $miniCss = new $class();
            foreach ($files as $file)
            {
                $miniCss->add($dir . '/' . $file);
            }

            $miniCss->minify($minDir . '/' . $asset);

            $results[$asset] = md5_file($minDir . '/' . $asset);
        }

        return $results;
    }

    //--------------------------------------------------------------------
}
