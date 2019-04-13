<?php namespace Michalsn\Minifier\Libraries;

use CodeIgniter\Config\BaseConfig;

class Minification
{
	/**
	 * Config object.
	 *
	 * @var BaseConfig
	 */
	protected $config;

	/**
	 * Error string.
	 *
	 * @var string
	 */
	protected $error = '';

	/**
	 * Data array.
	 *
	 * @var array
	 */
	protected $data = [];

	//--------------------------------------------------------------------

	/**
	 * Prepare assets to use on website
	 *
	 * @param BaseConfig $config
	 */
    public function __construct(BaseConfig $config)
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
		$ext = explode('.', $filename);
		$ext = end($ext);

		if (! in_array($ext, ['js', 'css']))
		{
			throw new Exception('Wrong file extension');
		}

		$versions = $this->version();

		$filenames = [];

		// do we use combined+minified+versioned assets?
		if ($this->config->minify)
		{
			if (isset($versions[$filename]))
			{
				$filenames[] = $filename . '?v=' . $versions[$filename];
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
		try
		{
			switch ($mode)
			{
				case 'js':
					$this->deployJs($this->config->js);
					break;
				case 'css':
					$this->deployCss($this->config->css);
					break;
				default:
					$this->deployJs($this->config->js);
					$this->deployCss($this->config->css);
			}

			$this->deployVersion($this->config->dirVersion);

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
	 * @return array
	 */
	protected function version(): array
	{
		static $versions = null;

		// load all versions numbers
		if ($versions === null)
		{
			$dir = rtrim($this->config->dirVersion, '/');

			if (! file_exists($dir.'/versions.json'))
			{
				throw new Exception('Versioning file not exists');
			}

			$versions = json_decode(file_get_contents($dir.'/versions.json'), true);
		}

		return $versions;
	}

	//--------------------------------------------------------------------

	/**
	 * Deploy version
	 *
	 * @return void
	 */
	protected function deployVersion()
	{
		$dir = rtrim($this->config->dirVersion, '/');

		file_put_contents($dir.'/versions.json', json_encode($this->data));
	}

	//--------------------------------------------------------------------

	/**
	 * Deploy JS
	 *
	 * @param array $assets JS assets
	 *
	 * @return void
	 */
	protected function deployJs(array $assets)
	{
		$dir = rtrim($this->config->dirJs, '/');

		foreach ($assets as $asset => $files)
		{
			$miniJs = new \MatthiasMullie\Minify\JS();
			foreach ($files as $file)
			{
				$miniJs->add($dir.'/'.$file);				
			}

			$miniJs->minify($dir.'/'.$asset);

			$this->data[$asset] = md5_file($dir.'/'.$asset);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Deploy CSS
	 *
	 * @param array $assets CSS assets
	 *
	 * @return void
	 */
	protected function deployCss(array $assets)
	{
		$dir = rtrim($this->config->dirCss, '/');

		foreach ($assets as $asset => $files)
		{
			$miniCss = new \MatthiasMullie\Minify\CSS();
			foreach ($files as $file)
			{
				$miniCss->add($dir.'/'.$file);
			}

			$miniCss->minify($dir.'/'.$asset);

			$this->data[$asset] = md5_file($dir.'/'.$asset);
		}
	}

	//--------------------------------------------------------------------
}