# Phalcon Library
Phalcon is a web framework delivered as a C extension providing high performance and lower resource consumption.

This is a repository to publish/share/experiment with new adapters, prototypes or functionality that can potentially be incorporated into the framework.

The code in this repository is written in PHP.

## Installation

### Installing via Composer

Install Composer in a common location or in your project:

```bash
curl -s http://getcomposer.org/installer | php
```

Create the `composer.json` file or add new require line as follows:

```json
{
    "require": {
        "manacode/phalconlibs": "dev-master"
    }
}
```

Run the composer installer:

```bash
$ php composer.phar install
```

### Installing via GitHub

Just clone the repository in a common location or inside your project:

```
git clone https://github.com/manacode/phalconlibs.git
```

## Autoloading the Library

Add or register the following namespace strategy to your Phalcon\Loader in order
to load classes from the phalconlibs repository:

```php

$loader = new Phalcon\Loader();

$loader->registerNamespaces([
    'Manacode\Phalcon' => '/path/to/manacode/phalconlibs/'
]);

$loader->register();
```

## Library Index

### Translate
* [Manacode\Phalcon\Translate\NativeArray](https://github.com/manacode/phalconlibs/blob/master/Translate/NativeArray.php) - Translation class using native array

## License

PhalconLibs is open-sourced software licensed under the [New BSD License](https://github.com/manacode/phalconlibs/blob/master/LICENSE). © Manacode Team and contributors
