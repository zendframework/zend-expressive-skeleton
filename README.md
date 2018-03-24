# Expressive Skeleton and Installer

[![Build Status](https://secure.travis-ci.org/zendframework/zend-expressive-skeleton.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-expressive-skeleton)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-expressive-skeleton/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-expressive-skeleton?branch=master)

*Begin developing PSR-15 middleware applications in seconds!*

[zend-expressive](https://github.com/zendframework/zend-expressive) builds on
[zend-stratigility](https://github.com/zendframework/zend-stratigility) to
provide a minimalist PSR-15 middleware framework for PHP with routing, DI
container, optional templating, and optional error handling capabilities.

This installer will setup a skeleton application based on zend-expressive by
choosing optional packages based on user input as demonstrated in the following
screenshot:

![screenshot-installer](https://cloud.githubusercontent.com/assets/459648/10410494/16bdc674-6f6d-11e5-8190-3c1466e93361.png)

The user selected packages are saved into `composer.json` so that everyone else
working on the project have the same packages installed. Configuration files and
templates are prepared for first use. The installer command is removed from
`composer.json` after setup succeeded, and all installer related files are
removed.

## Getting Started

Start your new Expressive project with composer:

```bash
$ composer create-project zendframework/zend-expressive-skeleton <project-path>
```

After choosing and installing the packages you want, go to the
`<project-path>` and start PHP's built-in web server to verify installation:

```bash
$ composer run --timeout=0 serve
```

You can then browse to http://localhost:8080.

> ### Linux users
>
> On PHP versions prior to 7.1.14 and 7.2.2, this command might not work as
> expected due to a bug in PHP that only affects linux environments. In such
> scenarios, you will need to start the [built-in web
> server](http://php.net/manual/en/features.commandline.webserver.php) yourself,
> using the following command:
>
> ```bash
> $ php -S 0.0.0.0:8080 -t public/ public/index.php
> ```

> ### Setting a timeout
>
> Composer commands time out after 300 seconds (5 minutes). On Linux-based
> systems, the `php -S` command that `composer serve` spawns continues running
> as a background process, but on other systems halts when the timeout occurs.
>
> As such, we recommend running the `serve` script using a timeout. This can
> be done by using `composer run` to execute the `serve` script, with a
> `--timeout` option. When set to `0`, as in the previous example, no timeout
> will be used, and it will run until you cancel the process (usually via
> `Ctrl-C`). Alternately, you can specify a finite timeout; as an example,
> the following will extend the timeout to a full day:
>
> ```bash
> $ composer run --timeout=86400 serve
> ```

## Troubleshooting

If the installer fails during the ``composer create-project`` phase, please go
through the following list before opening a new issue. Most issues we have seen
so far can be solved by `self-update` and `clear-cache`.

1. Be sure to work with the latest version of composer by running `composer self-update`.
2. Try clearing Composer's cache by running `composer clear-cache`.

If neither of the above help, you might face more serious issues:

- Info about the [zlib_decode error](https://github.com/composer/composer/issues/4121).
- Info and solutions for [composer degraded mode](https://getcomposer.org/doc/articles/troubleshooting.md#degraded-mode).

## Application Development Mode Tool

This skeleton comes with [zf-development-mode](https://github.com/zfcampus/zf-development-mode). 
It provides a composer script to allow you to enable and disable development mode.

### To enable development mode

**Note:** Do NOT run development mode on your production server!

```bash
$ composer development-enable
```

**Note:** Enabling development mode will also clear your configuration cache, to 
allow safely updating dependencies and ensuring any new configuration is picked 
up by your application.

### To disable development mode

```bash
$ composer development-disable
```

### Development mode status

```bash
$ composer development-status
```

## Configuration caching

By default, the skeleton will create a configuration cache in
`data/config-cache.php`. When in development mode, the configuration cache is
disabled, and switching in and out of development mode will remove the
configuration cache.

You may need to clear the configuration cache in production when deploying if
you deploy to the same directory. You may do so using the following:

```bash
$ composer clear-config-cache
```

You may also change the location of the configuration cache itself by editing
the `config/config.php` file and changing the `config_cache_path` entry of the
local `$cacheConfig` variable.

## Skeleton Development

This section applies only if you cloned this repo with `git clone`, not when you
installed expressive with `composer create-project ...`.

If you want to run tests against the installer, you need to clone this repo and
setup all dependencies with composer.  Make sure you **prevent composer running
scripts** with `--no-scripts`, otherwise it will remove the installer and all
tests.

```bash
$ composer update --no-scripts
$ composer test
```

Please note that the installer tests remove installed config files and templates
before and after running the tests.

Before contributing read [the contributing guide](docs/CONTRIBUTING.md).
