# atoum/apiblueprint-extension [![Build Status](https://travis-ci.org/Hywan/atoum-apiblueprint-extension.svg?branch=master)](https://travis-ci.org/Hywan/atoum-apiblueprint-extension)

[atoum](http://atoum.org/) is a PHP test
framework. [API Blueprint](https://apiblueprint.org/) is a high-level
HTTP API description language.

API Blueprint is only a text file. The goal of this atoum extension is
to compile API Blueprint files into executable tests. It works as any
test written with the atoum API, and it works within the atoum
ecosystem.

In addition, this atoum extension provides a very simple script to
render many API Blueprint files into a standalone HTML single-page
file.

## Installation

With [Composer](https://getcomposer.org/), to include this extension into
your dependencies, you need to
require
[`atoum/apiblueprint-extension`](https://packagist.org/packages/atoum/apiblueprint-extension):

```sh
$ composer require atoum/apiblueprint-extension '~0.1'
```

To enable the extension, the `.atoum.php` configuration file must be edited to add:

```php
// Enable the extension.
$extension = new atoum\apiblueprint\extension($script);
$extension->addToRunner($runner);

// Compile files from the `apiblueprints` directory to executable tests.
$extension->getAPIBFinder()->append(new FilesystemIterator('./apiblueprints'));
$extension->compileAndEnqueue();
```

## Testing

Before running the test suites, the development dependencies must be installed:

```sh
$ composer install
```

Then, to run all the test suites:

```sh
$ composer test
```

# License

Please, see the `LICENSE` file. This project uses the same license than atoum.
