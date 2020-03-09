<?php namespace Michalsn\Minifier;

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
	}

	//--------------------------------------------------------------------

	/**
	 * Load minified file
	 *
	 * @param string $filename File name
	 *
	 * @return string
	 */
	public function load(string $filename): string
	{
		// determine file extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if (! in_array($ext, ['js', 'css']))
		{
			throw MinifierException::forWrongFileExtension($ext);
		}

		$versions = $this->getVersion($this->config->dirVersion);

		if (empty($versions))
		{
			throw MinifierException::forNoVersioningFile();
		}

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

		// determine file folder
		$dir = ($ext === 'js') ? $this->config->dirJs : $this->config->dirCss;
		$dir = ltrim(trim($dir, '/'), './');

		// add base url if needed
		if ($this->config->baseUrl !== null)
		{
			$dir = rtrim($this->config->baseUrl, '/') . '/' . $dir;
		}

		// prepare output
		$output = '';

		foreach ($filenames as $file)
		{
			$output .= sprintf($tag, $dir . '/' . $file) . PHP_EOL;
		}

		return $output;
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
			throw MinifyException::forIncorrectDeploymentMode($mode);
		}

		$files = [];

		try
		{
			switch ($mode)
			{
				case 'js':
					$files = $this->deployJs($this->config->js, $this->config->dirJs);
					break;
				case 'css':
					$files = $this->deployCss($this->config->css, $this->config->dirCss);
					break;
				default:
					$files['js']  = $this->deployJs($this->config->js, $this->config->dirJs);
					$files['css'] = $this->deployCss($this->config->css, $this->config->dirCss);
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
	 * Load version file
	 *
	 * @param string $dir Directory
	 *
	 * @return array
	 */
	protected function getVersion(string $dir, $silent = false): array
	{
		static $versions = null;

		// load all versions numbers
		if ($versions === null)
		{
			$dir = rtrim($dir, '/');

			if (! file_exists($dir . '/versions.json'))
			{
				if ($silent === false)
				{
					throw MinifierException::forNoVersioningFile();
				}
				else
				{
					return [];
				}
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

		$version = $this->getVersion($dir, true);

		if ($mode === 'all')
		{
			$version = $files;
		}
		else
		{
			$version[$mode] = $files;
		}

		file_put_contents($dir . '/versions.json', json_encode($version));
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
	protected function deployJs(array $assets, string $dir): array
	{
		$dir = rtrim($dir, '/');

		$results = [];

		foreach ($assets as $asset => $files)
		{
			$miniJs = new \MatthiasMullie\Minify\JS();
			foreach ($files as $file)
			{
				$miniJs->add($dir . '/' . $file);				
			}

			$miniJs->minify($dir . '/' . $asset);

			$results[$asset] = md5_file($dir . '/' . $asset);
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
	protected function deployCss(array $assets, string $dir): array
	{
		$dir = rtrim($dir, '/');

		$results = [];

		foreach ($assets as $asset => $files)
		{
			$miniCss = new \MatthiasMullie\Minify\CSS();
			foreach ($files as $file)
			{
				$miniCss->add($dir . '/' . $file);
			}

			$miniCss->minify($dir . '/' . $asset);

			$results[$asset] = md5_file($dir . '/' . $asset);
		}

		return $results;
	}

	//--------------------------------------------------------------------
}