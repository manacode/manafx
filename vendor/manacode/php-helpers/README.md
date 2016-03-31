# PHP Helpers

This is a collection of PHP helpers class that can potentially be incorporated into your PHP application.

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
        "manacode/php-helpers": "dev-master"
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
git clone https://github.com/manacode/php-helpers.git
```

## Autoloading the Library

Add or register the following namespace strategy to your application loader in order
to load classes from the php-helpers repository:

```php

if (file_exists(VENDOR_PATH . 'autoload.php')) {
	$vendor = include VENDOR_PATH . 'autoload.php';
}
```

## Library Index

### CountryList
* [Manacode\Helpers\CountryList](https://github.com/manacode/php-helpers/blob/master/src/CountryList.php) - Country list class

## License

php-helpers is open-sourced software licensed under the [New BSD License](https://github.com/manacode/php-helpers/blob/master/LICENSE). © Manacode Team and contributors
