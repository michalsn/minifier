# CodeIgniter Minifier Documentation

Asset minification and versioning library for CodeIgniter 4.

### Requirements

![PHP](https://img.shields.io/badge/PHP-%5E8.1-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%5E4.1-blue)

## Overview

We can define files we want to combine and minify:

```php
public $js = [
    'all.min.js' => ['file1.js', 'file2.js']
];
```

And then use it with only one line:

```php
echo minifier('all.min.js');
```

This will return ready to use HTML tag:

```html
<script src="http://localhost/assets/js/all.min.js?v=9ef881911da8d7c4a1c2f19c4878d122" type="text/javascript"></script>
```

