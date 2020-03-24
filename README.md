# Minifier

This library for CodeIgniter4 offers a minification and versioning for CSS and JS files.

## Installation via composer

	> composer require michalsn/minifier

## Manual installation

Download this repo and then enable it by editing **app/Config/Autoload.php** and adding the **Michalsn\Minifier**
namespace to the **$psr4** array. For example, if you copied it into **app/ThirdParty**:

```php
$psr4 = [
    'Config'      => APPPATH . 'Config',
    APP_NAMESPACE => APPPATH,
    'App'         => APPPATH,
    'Michalsn\Minifier' => APPPATH .'ThirdParty/minifier/src',
];
```
## Configuration

Run command:

	> php spark minify:publish

This command will copy a config file to your app namespace.
Then you can adjust it to your needs. By default file will be present in `app/Config/Minifier.php`.

You should define an array of files that you want to minify, ie:

```php
'all.min.js' => [
	'jquery-3.2.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
]
```

or

```php
'all.min.css' => [
	'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
]
```

This way requesting for a `all.min.js` or `all.min.css` file will return a minified and combined version of all files in a given array.

## Usage

To actually minify all the files we have to run command:

	> php spark minify:all

This will prepare everything and will set up a versioning.
Now to generate a proper tag with desired file to load, you have to make a simple call in your code:

```php
minifier('all.min.js');
```

or

```php
minifier('all.min.css');
```



