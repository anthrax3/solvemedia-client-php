# solvemedia-client-php

[![Build Status](https://travis-ci.org/traderinteractive/solvemedia-client-php.svg?branch=master)](https://travis-ci.org/traderinteractive/solvemedia-client-php)
[![Code Quality](https://scrutinizer-ci.com/g/traderinteractive/solvemedia-client-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/traderinteractive/solvemedia-client-php/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/traderinteractive/solvemedia-client-php/badge.svg?branch=master)](https://coveralls.io/github/traderinteractive/solvemedia-client-php?branch=master)

[![Latest Stable Version](https://poser.pugx.org/traderinteractive/solvemedia-client/v/stable)](https://packagist.org/packages/traderinteractive/solvemedia-client)
[![Latest Unstable Version](https://poser.pugx.org/traderinteractive/solvemedia-client/v/unstable)](https://packagist.org/packages/traderinteractive/solvemedia-client)
[![License](https://poser.pugx.org/traderinteractive/solvemedia-client/license)](https://packagist.org/packages/traderinteractive/solvemedia-client)

[![Total Downloads](https://poser.pugx.org/traderinteractive/solvemedia-client/downloads)](https://packagist.org/packages/traderinteractive/solvemedia-client)
[![Monthly Downloads](https://poser.pugx.org/traderinteractive/solvemedia-client/d/monthly)](https://packagist.org/packages/traderinteractive/solvemedia-client)
[![Daily Downloads](https://poser.pugx.org/traderinteractive/solvemedia-client/d/daily)](https://packagist.org/packages/traderinteractive/solvemedia-client)

A PHP client for the Solve Media CAPTCHA API.

## Requirements

solvemedia-client-php requires PHP 7.0 (or later).

## Composer
To add the library as a local, per-project dependency use [Composer](http://getcomposer.org)! Simply add a dependency on
`traderinteractive/solvemedia-client` to your project's `composer.json` file such as:

```sh
composer require traderinteractive/solvemedia-client
```

## Documentation

Found in the [source](src) itself, take a look!

## Contact

Developers may be contacted at:

 * [Pull Requests](https://github.com/traderinteractive/solvemedia-client-php/pulls)
 * [Issues](https://github.com/traderinteractive/solvemedia-client-php/issues)

## Project Build

With a checkout of the code get [Composer](http://getcomposer.org) in your PATH and run:

```sh
./vendor/bin/phpunit
./vendor/bin/phpcs
```

There is also a [docker](http://www.docker.com/)-based [fig](http://www.fig.sh/) configuration that will execute the build inside a docker container.  This is an easy way to build the application:

```sh
fig run build
```

For more information on our build process, read through out our [Contribution Guidelines](.github/CONTRIBUTING.md).
