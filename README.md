# atoum/apiblueprint-extension [![Build Status](https://travis-ci.org/Hywan/atoum-apiblueprint-extension.svg?branch=master)](https://travis-ci.org/Hywan/atoum-apiblueprint-extension)

…

## Installation

With [Composer](https://getcomposer.org/), to include this extension into
your dependencies, you need to
require
[`atoum/apiblueprint-extension`](https://packagist.org/packages/atoum/apiblueprint-extension):

```sh
$ composer require atoum/apiblueprint-extension '~0.0'
```

To always enable the extension, the `.atoum.php` configuration file must be edited to add:

```php
use atoum\apiblueprint;

$extension = new apiblueprint\extension($script);
$extension->addToRunner($runner);
```

## Testing

Before running the test suites, the development dependencies must be installed:

```sh
$ composer install
```

Then, to run all the test suites:

```sh
$ vendor/bin/atoum --test-ext
```

# License

Please, see the `LICENSE` file. This project uses the same license than atoum.
