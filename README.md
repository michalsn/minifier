# Minifier

Asset minification and versioning library for CodeIgniter 4.

[![PHPUnit](https://github.com/michalsn/minifier/actions/workflows/phpunit.yml/badge.svg)](https://github.com/michalsn/minifier/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/michalsn/minifier/actions/workflows/phpstan.yml/badge.svg)](https://github.com/michalsn/minifier/actions/workflows/phpstan.yml)
[![Deptrac](https://github.com/michalsn/minifier/actions/workflows/deptrac.yml/badge.svg)](https://github.com/michalsn/minifier/actions/workflows/deptrac.yml)
[![Coverage Status](https://coveralls.io/repos/github/michalsn/minifier/badge.svg?branch=develop)](https://coveralls.io/github/michalsn/minifier?branch=develop)

![PHP](https://img.shields.io/badge/PHP-%5E8.1-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%5E4.1-blue)

## Installation

```console
composer require michalsn/minifier
```

## Configuration

Run command:

```console
php spark minify:publish
```

This command will copy a config file to your app namespace.
Then you can adjust it to your needs. By default, file will be present in `app/Config/Minifier.php`.

You should define an array of files that you want to minify, ie:

```php
public $js = [
    'all.min.js' => [
        'jquery-3.7.1.min.js', 'bootstrap-3.3.7.min.js', 'main.js',
    ]
];

// or / and

public $css = [
    'all.min.css' => [
        'bootstrap-3.3.7.min.css', 'font-awesome-4.7.0.min.css', 'main.css',
    ]
];
```

This way requesting for a `all.min.js` or `all.min.css` file will return a minified and combined version of all files in a given array.

## Docs

Read the full documentation: https://michalsn.github.io/minifier/

