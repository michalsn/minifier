# Configuration

## Config File

To make changes to the config file, we have to have our copy in the `app/Config/Minifier.php`. Luckily, this package comes with handy command that will make this easy.

When we run:

```console
php spark minify:publish
```

We will get our copy ready for modifications.

Now, you should define an array of files that you want to minify, ie:

```php
public array $js = [
    'all.min.js' => [
        'jquery-3.7.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
    ]
];

// or / and

public array $css = [
    'all.min.css' => [
        'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
    ]
];
```

This way requesting for a `all.min.js` or `all.min.css` file will return a minified and combined version of all files in a given array.

## Config Options

### $minify

Use this variable to turn on and off minification of the assets. Turning off can be useful during app development - for easy debugging.

**Available options**: `true`, `false`

**Default value**: `true`

### $baseUrl

Use this variable when you want to set absolute path to the asset files. If no other URLs are set, like `$baseJsUrl` or `$baseCssUrl` then values set to `$dirJS` and `$dirCss` will be added to the final URL.

**Default value**: `null`

### $baseJsUrl

Use this variable when your JS assets are served from subdomain. Keep in mind that in this case variable `$dirJs` won't be added to the URL.

**Default value**: `null`

### $baseCssUrl

Use this variable when your CSS assets are served from subdomain. Keep in mind that in this case variable `$dirCSS` won't be added to the URL.

**Default value**: `null`

### $adapterJs

Adapter to use for minifying JS files. You can also implement your own JS adapter to minify assets and replace this class.

**Default value**: `\Michalsn\Minifier\Adapters\Js\MinifyAdapter::class`

### $adapterCss

Adapter to use for minifying CSS files. You can also implement your own CSS adapter to minify assets and replace this class.

**Default value**: `\Michalsn\Minifier\Adapters\Css\MinifyAdapter::class`

### $dirJs

JS assets directory.

**Default value**: `./assets/js`

### $dirCss

CSS assets directory.

**Default value**: `./assets/css`

### $dirMinJs

Minified JS asset directory. If not set the value from `$dirJs` will be used instead.

**Default value**: `null`

### $dirMinCss

Minified CSS asset directory. If not set the value from `$dirCss` will be used instead.

**Default value**: `null`

### $dirVersion

Directory to store assets versioning.

**Default value**: `./assets`

### $tagJs

JS tag to use in HTML when displaying JS assets.

**Default value**: `<script type="text/javascript" src="%s"></script>`

### $tagCss

CSS tag to use in HTML when displaying CSS assets.

**Default value**: `<link rel="stylesheet" href="%s">`

### $returnType

Determines how the files will be returned. The default value is `html` and it uses the `$tagJs` and `$tagCss` variables. Using `array` will return the php array and `json` type will return a json string.

**Available options**: `html`, `json`, `array`

**Default value**: `html`

### $autoDeployOnChange

Specifies if we want to automatically deploy whenever there is a change to any of our assets files. Keep in mind that enabling this feature will have an impact on performance.

**Available options**: `true`, `false`

**Default value**: `false`

### $js

This array defines JS files to minify.

### $css

This array defines CSS files to minify.
