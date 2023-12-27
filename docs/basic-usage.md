# Basic Usage

After [defining our files](configuration.md#config-file) we want to actually minify all the files we have. This is easy enough as running the command:

```console
php spark minify:all
```

This will prepare everything and will set up a versioning. Make sure to load a minifier helper in your controller, by calling:

```php
helper('minifier');
```

Now to generate a proper tag with desired file to load, you have to make a simple call in your code:

```php
minifier('all.min.js');

// or / and

minifier('all.min.css');
```

This will produce:

```html
<script type="text/javascript"
        src="http://localhost/assets/js/all.min.js?v=9ef881911da8d7c4a1c2f19c4878d122"></script>

<!-- or / and -->

<link rel="stylesheet"
      href="http://localhost/assets/css/all.min.css?v=50a35b0b1d1c3798aa556b8245314930">
```
