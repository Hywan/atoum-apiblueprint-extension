<p align="center">
  <img src="./res/logo.png" alt="atoum's extension logo" width="200" />
</p>

# atoum/apiblueprint-extension [![Build Status](https://travis-ci.org/Hywan/atoum-apiblueprint-extension.svg?branch=master)](https://travis-ci.org/Hywan/atoum-apiblueprint-extension)

[atoum](http://atoum.org/) is a PHP test
framework. [API Blueprint](https://apiblueprint.org/) is a high-level
HTTP API description language.

API Blueprint is only a text file. The goal of this atoum extension is
to **compile API Blueprint files into executable tests**. It works as
any test written with the atoum API, and it works within the atoum
ecosystem.

In addition, this atoum extension provides a very simple script to
**render** many API Blueprint files into a standalone HTML single-page
file.

<p align="center">
  <img src="./res/overview.svg" alt="Process overview" width="580" />
</p>

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

### JSON schemas defined outside `.apib` files

It is possible to define JSON schemas outside the `.apib` files. To do
so, you must go through these 2 steps:

  1. Mount a JSON schema directory on the extension's configuration,
  2. Use `{"$ref": "json-schema://<mount>/schema.json"}` in the Schema
     section of the API Blueprint documentation.
     
Example:

  1. ```php
     // .atoum.php
     $extension->getConfiguration()->mountJsonSchemaDirectory('test', '/path/to/schemas/');
     ```
  2. ```apib
     // my-spec.apib
     + Response 200

       + Schema

         {"$ref": "json-schema://test/my-schema.json"}
     ```
     where `test` is the “mount name”, and `my-schema.json` is a valid
     JSON schema in `/path/to/schemas/my-schema.json`.

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
