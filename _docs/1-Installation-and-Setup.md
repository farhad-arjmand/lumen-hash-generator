# 1. Installation

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

## Server Requirements

The package has a few system requirements:

    - PHP >= 7.0

## Composer

You can install this package via [Composer](http://getcomposer.org/) by running this command:

```bash
$ composer require farhad-arjmand/lumen-hash-generator
```

## Lumen

### Setup

> **NOTE :** The package will automatically register itself if you're using Lumen `>= v5.5`, so you can skip this section.

Once the package is installed, you can register the service provider in `bootstrap/app.php`:

```php
$app->register(FarhadArjmand\LumenHashGenerator\HashServiceProvider::class);
```

### Artisan commands

Run the following command to publish the package config file:

```bash
$ php artisan vendor:publish --provider="FarhadArjmand\LumenHashGenerator\HashServiceProvider"
```

You should now have a config/hash.php file that allows you to configure the basics of this package.

### Running Migrations
To create `users` table, execute the `migrate` Artisan command:

```bash
$ php artisan migrate
```