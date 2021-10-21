<?php

use Michalsn\Minifier\Minifier;
use Michalsn\Minifier\Exceptions\MinifierException;

class MinifierTest extends \CodeIgniter\Test\CIUnitTestCase
{
    protected $config;

    protected $minifier;

    protected $ver = [
        'js'  => '9ef881911da8d7c4a1c2f19c4878d122',
        'css' => '95cb11cf55b3f1164e80ae9393644ae3'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->config = new \Michalsn\Minifier\Config\Minifier();

        $this->config->baseUrl = 'http://localhost/';
        $this->config->dirJs = SUPPORTPATH . 'assets/js';
        $this->config->dirCss = SUPPORTPATH . 'assets/css';
        $this->config->dirMinJs = SUPPORTPATH . 'assets/js';
        $this->config->dirMinCss = SUPPORTPATH . 'assets/css';
        $this->config->dirVersion = SUPPORTPATH . 'assets';
        $this->config->js = ['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']];
        $this->config->css = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']];

        if (file_exists($this->config->dirJs . '/new.js'))
        {
            unlink($this->config->dirJs . '/new.js');
        }

        if (file_exists($this->config->dirCss . '/new.css'))
        {
            unlink($this->config->dirCss . '/new.css');
        }
/*
        if (file_exists($this->config->dirVersion . '/versions.js'))
        {
            unlink($this->config->dirVersion . '/versions.js');
        }
*/
    }

    public function testConfig()
    {
        $this->assertTrue($this->config instanceof \Michalsn\Minifier\Config\Minifier);

        $this->assertEquals('<script type="text/javascript" src="%s"></script>', $this->config->tagJs);
        $this->assertEquals('<link rel="stylesheet" href="%s">', $this->config->tagCss);

        $this->assertEquals(['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']], $this->config->js);
        $this->assertEquals(['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']], $this->config->css);
    }

    public function testDeployExceptionForIncorrectDeploymentMode()
    {
        $this->expectException(MinifierException::class);
        $this->expectExceptionMessage('The "incorrect" is not correct deployment mode');

        $this->minifier = new Minifier($this->config);

        $this->minifier->deploy('incorrect');
    }

    public function testLoadExceptionForMissingVersioningFile()
    {
        $this->expectException(MinifierException::class);
        $this->expectExceptionMessage('There is no file with versioning. Run "php spark minify:all');

        if (file_exists($this->config->dirVersion . '/versions.json'))
        {
            unlink($this->config->dirVersion . '/versions.json');
        }

        $this->minifier = new Minifier($this->config);

        $this->minifier->load('all.min.css');
    }

    public function testLoadExceptionForWrongFileExtension()
    {
        $this->expectException(MinifierException::class);
        $this->expectExceptionMessage('Wrong file extension: ".php".');

        $this->minifier = new Minifier($this->config);

        $this->minifier->load('all.min.php');
    }

    public function testDeployJs()
    {
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('js');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirJs . DIRECTORY_SEPARATOR .  array_key_first($this->config->js)));
    }

    public function testDeployCss()
    {
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('css');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirCss . DIRECTORY_SEPARATOR .  array_key_first($this->config->css)));
    }

    public function testDeployAll()
    {
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('all');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirJs . DIRECTORY_SEPARATOR .  array_key_first($this->config->js)));
        $this->assertTrue(file_exists($this->config->dirCss . DIRECTORY_SEPARATOR .  array_key_first($this->config->css)));
    }



    public function testLoadJs()
    {
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals('<script type="text/javascript" src="http://localhost' . SUPPORTPATH . 'assets/js/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCss()
    {
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals('<link rel="stylesheet" href="http://localhost' . SUPPORTPATH . 'assets/css/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithDirMinJs()
    {
        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals('<script type="text/javascript" src="http://localhost' . SUPPORTPATH . 'public/js/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithDirMinCss()
    {
        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals('<link rel="stylesheet" href="http://localhost' . SUPPORTPATH . 'public/css/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithBaseJsUrl()
    {
        $this->config->baseJsUrl = 'http://js.localhost/';

        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals('<script type="text/javascript" src="http://js.localhost/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithBaseCssUrl()
    {
        $this->config->baseCssUrl = 'http://css.localhost/';

        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals('<link rel="stylesheet" href="http://css.localhost/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithBaseJsUrlAndDirMinJs()
    {
        $this->config->baseJsUrl = 'http://js.localhost/';
        $this->config->dirMinJs = SUPPORTPATH . 'public/js';

        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals('<script type="text/javascript" src="http://js.localhost/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithBaseCssUrlAndDirMinCss()
    {
        $this->config->baseCssUrl = 'http://css.localhost/';
        $this->config->dirMinCss = SUPPORTPATH . 'public/css';

        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals('<link rel="stylesheet" href="http://css.localhost/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testJsonReturnTypeWithLoadJs()
    {
        $this->config->returnType = 'json';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals(json_encode(['http://localhost' . SUPPORTPATH . 'assets/js/all.min.js?v=' . $this->ver['js']]), $result);
    }

    public function testJsonReturnTypeWithLoadCss()
    {
        $this->config->returnType = 'json';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals(json_encode(['http://localhost' . SUPPORTPATH . 'assets/css/all.min.css?v=' . $this->ver['css']]), $result);
    }

    public function testArrayReturnTypeWithLoadJs()
    {
        $this->config->returnType = 'array';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.js');

        $this->assertEquals(['http://localhost' . SUPPORTPATH . 'assets/js/all.min.js?v=' . $this->ver['js']], $result);
    }

    public function testArrayReturnTypeWithLoadCss()
    {
        $this->config->returnType = 'array';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->load('all.min.css');

        $this->assertEquals(['http://localhost' . SUPPORTPATH . 'assets/css/all.min.css?v=' . $this->ver['css']], $result);
    }

    public function testLoadExceptionForWrongReturnType()
    {
        $this->expectException(MinifierException::class);
        $this->expectExceptionMessage('The "php" is not correct return type.');

        $this->config->returnType = 'php';
        $this->minifier = new Minifier($this->config);

       $this->minifier->load('all.min.css');
    }

    public function testAutoDeployOnChangeJsFalse()
    {
        $this->config->autoDeployOnChange = true;
        $this->config->js = ['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js']];
        $this->minifier = new Minifier($this->config);

        $method = $this->getPrivateMethodInvoker($this->minifier, 'autoDeployCheckJs');

        $this->assertFalse($method('all.min.js'));
    }

    public function testAutoDeployOnChangeJsTrue()
    {
        $this->config->autoDeployOnChange = true;
        $this->config->js = ['all.min.js' => ['bootstrap.js', 'jquery-3.4.1.js', 'main.js', 'new.js']];
        $this->minifier = new Minifier($this->config);

        sleep(1);
        file_put_contents($this->config->dirJs . '/new.js', '//data;');

        $method = $this->getPrivateMethodInvoker($this->minifier, 'autoDeployCheckJs');

        $this->assertTrue($method('all.min.js'));
    }

    public function testAutoDeployOnChangeCssFalse()
    {
        $this->config->autoDeployOnChange = true;
        $this->config->css = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']];
        $this->minifier = new Minifier($this->config);

        $method = $this->getPrivateMethodInvoker($this->minifier, 'autoDeployCheckCss');

        $this->assertFalse($method('all.min.css'));
    }

    public function testAutoDeployOnChangeCssTrue()
    {
        $this->config->autoDeployOnChange = true;
        $this->config->css = ['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css', 'new.css']];
        $this->minifier = new Minifier($this->config);

        sleep(1);
        file_put_contents($this->config->dirCss . '/new.css', '//data;');

        $method = $this->getPrivateMethodInvoker($this->minifier, 'autoDeployCheckCss');

        $this->assertTrue($method('all.min.css'));
    }

    public function testDeployJsWithDirMinJs()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js'))
        {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('js');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirMinJs . DIRECTORY_SEPARATOR .  array_key_first($this->config->js)));
    }

    public function testDeployCssWithDirMinCss()
    {
        if (file_exists($this->config->dirMinCss . '/all.min.css'))
        {
            unlink($this->config->dirMinCss . '/all.min.css');
        }

        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('css');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirMinCss . DIRECTORY_SEPARATOR .  array_key_first($this->config->css)));
    }

    public function testDeployAllWithDirMinJsAndCss()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js'))
        {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        if (file_exists($this->config->dirMinCss . '/all.min.css'))
        {
            unlink($this->config->dirMinCss . '/all.min.css');
        }

        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifier = new Minifier($this->config);

        $result = $this->minifier->deploy('all');

        $this->assertTrue($result);

        $this->assertTrue(file_exists($this->config->dirMinJs . DIRECTORY_SEPARATOR .  array_key_first($this->config->js)));
        $this->assertTrue(file_exists($this->config->dirMinCss . DIRECTORY_SEPARATOR .  array_key_first($this->config->css)));
    }
}
