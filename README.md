# Minifier [![](https://github.com/michalsn/minifier/workflows/PHP%20Tests/badge.svg)](https://github.com/michalsn/minifier/actions?query=workflow%3A%22PHP+Tests%22)

Asset minification and versioning library for CodeIgniter 4.

## Installation

    > composer require michalsn/minifier

## Configuration

Run command:

    > php spark minify:publish

This command will copy a config file to your app namespace.
Then you can adjust it to your needs. By default, file will be present in `app/Config/Minifier.php`.

You should define an array of files that you want to minify, ie:

```php
public $js = [
    'all.min.js' => [
        'jquery-3.2.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
    ]
];
```

or

```php
public $css = [
    'all.min.css' => [
        'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
    ]
];
```

This way requesting for a `all.min.js` or `all.min.css` file will return a minified and combined version of all files in a given array.

## Usage

To actually minify all the files we have to run command:

    > php spark minify:all

This will prepare everything and will set up a versioning. Make sure to load a minifier helper in your controller, by calling:

```php
helper('minifier');
```

Now to generate a proper tag with desired file to load, you have to make a simple call in your code:

```php
minifier('all.min.js');
```

or

```php
minifier('all.min.css');
```

This will produce:

```html
<script type="text/javascript" src="http://localhost/assets/js/all.min.js?v=9ef881911da8d7c4a1c2f19c4878d122"></script> 
```

or

```html
<link rel="stylesheet" href="http://localhost/assets/css/all.min.css?v=50a35b0b1d1c3798aa556b8245314930">
```

## Config options

After running command:

    > php spark minify:publish

You can modify the config file which by default is copied to the path `app/Config/Minifier.php`. Here are your options with a description.

Variable | Default value | Options | Desctiption
-------- | ------------- | ------- | -----------
`$minify`| `true` | `true`, `false` | Use this variable to turn on and off minification of the assets. Turning off can be useful during app development - for easy debugging.
`$baseUrl` | `null` | | Use this variable when you want to set absolute path to the asset files. If no other URLs are set, like `$baseJsUrl` or `$baseCssUrl` then values set to `$dirJS` and `$dirCss` will be added to the final URL.
`$baseJsUrl` | `null` | | Use this variable when your JS assets are served from subdomain. Bare in mind that in this case variable `$dirJs` won't be added to the URL.
`$baseCssUrl` | `null` | | Use this variable when your CSS assets are served from subdomain. Bare in mind that in this case variable `$dirCSS` won't be added to the URL.
`$adapterJs` | `\Michalsn\Minifier\Adapters\Js\MinifyAdapter::class` | | Adapter to use for minifying JS files. You can also implement your own JS adapter to minify assets and replace this class.
`$adapterCss` | `\Michalsn\Minifier\Adapters\Css\MinifyAdapter::class` | | Adapter to use for minifying CSS files. You can also implement your own CSS adapter to minify assets and replace this class.
`$dirJs` | `./assets/js` | | JS assets directory.
`$dirCss` | `./assets/css` | | CSS assets directory.
`$dirMinJs` | `null` | | Minified JS asset directory. If not set the value from `$dirJs` will be used instead.
`$dirMinCss` | `null` | | Minified CSS asset directory. If not set the value from `$dirCss` will be used instead.
`$dirVersion` | `./assets` | | Directory to store assets versioning.
`$tagJs` | `<script type="text/javascript" src="%s"></script>` | | JS tag to use in HTML when displaying JS assets.
`$tagCss` | `<link rel="stylesheet" href="%s">` | | CSS tag to use in HTML when displaying CSS assets.
`$returnType` | `html` | `html`, `json`, `array` | Determines how the files will be returned. The dafault value is `html` and it uses the `$tagJs` and `$tagCss` variables. Using `array` will return the php array and `json` type will return a json string.
`$autoDeployOnChange` | `false` | `true`, `false` | Specifies if we want to automatically deploy whenever there is a change to any of our assets files. Keep in mind that enabling this feature will have an impact on performance.
`$js` | | | This array defines JS files to minify.
`$css` | | | This array defines CSS files to minify.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

