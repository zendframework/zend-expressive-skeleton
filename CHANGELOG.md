# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.1.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.6 - TBD

### Added

- Nothing.

### Changed

- [#251](https://github.com/zendframework/zend-expressive-skeleton/pull/251)
  updates the minimum version of northwoods/container to version 3.0.0,
  as that version now passes all DI container configuration compatibility tests.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.5 - 2018-04-11

### Added

- Nothing.

### Changed

- [#250](https://github.com/zendframework/zend-expressive-skeleton/pull/250)
  updates the minimum version of jsoumelidis/zend-sf-di-config to version 0.3.0,
  as that version now passes all DI container configuration compatibility tests.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.4 - 2018-03-28

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixes a schema violation with the shipped `composer.json` that was preventing
  Packagist from accepting the package.

## 3.0.3 - 2018-03-27

### Added

- Nothing.

### Changed

- [#246](https://github.com/zendframework/zend-expressive-skeleton/pull/246)
  moves contributing documentation into the `docs/` tree, and adds more
  documentation for that context (support document, issue and pull request
  templates). These changes also allow a simplified mechanism for removing these
  from the tree after initial install, allowing users to define appropriate
  versions for their own project.

- [#247](https://github.com/zendframework/zend-expressive-skeleton/pull/247)
  bumps the minimum supported version of jsoumelidis/zend-sf-di-config package
  to `^0.2` (from `^0.1`). If you were using a previous version with a project
  you have created, you can update manually using:

  ```bash
  $ composer require "jsoumelidis/zend-sf-di-config:^0.2"
  ```

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.2 - 2018-03-21

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#244](https://github.com/zendframework/zend-expressive-skeleton/pull/244)
  fixes an issue with installer prompts when symfony/console v4 is installed
  globally.

## 3.0.1 - 2018-03-19

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#242](https://github.com/zendframework/zend-expressive-skeleton/pull/242)
  updates the "container" links within the shipped home page templates to
  reference PSR-11 instead of container-interop.

## 3.0.0 - 2018-03-15

### Added

- [#185](https://github.com/zendframework/zend-expressive-skeleton/pull/185),
  [#200](https://github.com/zendframework/zend-expressive-skeleton/pull/200)
  adds PSR-15 support.

- [#161](https://github.com/zendframework/zend-expressive-skeleton/pull/161)
  adds support for Auryn to be used as a container via a wrapper.

- [#182](https://github.com/zendframework/zend-expressive-skeleton/pull/182)
  adds Symfony DI container support.

- [#212](https://github.com/zendframework/zend-expressive-skeleton/pull/212)
  adds the Composer script "expressive", which will invoke the "expressive"
  command from the zend-expressive-tooling package:

  ```bash
  $ composer expressive help
  ```

- [#215](https://github.com/zendframework/zend-expressive-skeleton/pull/215)
  adds packages to the zend-component-installer whitelist to prevent prompts for
  configuration provider injection.

- [#224](https://github.com/zendframework/zend-expressive-skeleton/pull/224)
  adds notifications for whitelisted packages.

- [#238](https://github.com/zendframework/zend-expressive-skeleton/pull/238)
  adds links to documentation for the specific container installed to the
  shipped home page.

### Changed

- The skeleton now requires zend-expressive 3.0; for detailed changes, see the
  [zend-expressive 3.0.0 changelog](https://github.com/zendframework/zend-expressive/releaes/3.0.0).

- [#212](https://github.com/zendframework/zend-expressive-skeleton/pull/212)
  makes the zend-expressive-tooling package an explicit development requirement;
  it is no longer an optional package.

- [#213](https://github.com/zendframework/zend-expressive-skeleton/pull/213)
  updates how the `routes.php` and `pipeline.php` files are defined. They now
  return anonymous functions with the following signature:

  ```php
  function (
      Zend\Expressive\Application $app,
      Zend\Expressive\MiddlewareFactory $factory,
      Psr\Container\ContainerInterface $container
  ) : void
  ```

  The `public/index.php` file now does the following:

  ```php
  $app = $container->get(\Zend\Expressive\Application::class);
  $factory = $container->get(\Zend\Expressive\MiddlewareFactory::class);
  (require 'config/pipeline.php')($app, $factory, $container);
  (require 'config/routes.php')($app, $factory, $container);
  ```

  This approach allows users to pull other dependencies as needed, without
  cluttering the global namespace, and to use the `MiddlewareFactory` features
  along with features such as the `Zend\Stratigility\path()` and `host()`
  utility methods.

- [#214](https://github.com/zendframework/zend-expressive-skeleton/pull/214)
  renames the shipped "Action" namespace and classes to use the verbiage
  "Handler" (for consistency with PSR-15), and be implemented as PSR-15
  `RequestHandlerInterface` implementations.

- [#197](https://github.com/zendframework/zend-expressive-skeleton/pull/197)
  updates `public/index.php` to remove `call_user_func()` in favor of direct
  callable invocation (e.g., `(function () { /* ... */ })()`).

- [#177](https://github.com/zendframework/zend-expressive-skeleton/pull/177)
  moves Aura.Di and Pimple container configuration to separate repositories.

- [#201](https://github.com/zendframework/zend-expressive-skeleton/pull/201)
  updates the default assets to Bootstrap 4, jQuery 3.3.1 and Font-Awesome 5.

- [#202](https://github.com/zendframework/zend-expressive-skeleton/pull/202),
  [#205](https://github.com/zendframework/zend-expressive-skeleton/pull/205)
  uses ConfigProviders to setup components. To make sure all ConfigProviders
  are loaded you need to answer yes to all inject
  `Zend\<component>\ConfigProvider` questions or do this once and select for
  all other packages.

- [#199](https://github.com/zendframework/zend-expressive-skeleton/pull/199)
  moves the location of the configuration cache from `data/config-cache.php` to
  `data/cache/config-cache.php`. Since the shipped `composer clear-config-cache`
  script and zf-development-mode both use the `$config['config_cache_path']`
  setting to determine where the cache file lives, this should have no bearing
  on normal, documented usage.

- [#219](https://github.com/zendframework/zend-expressive-skeleton/pull/219)
  updates templates such that all example assets (css, images, and javascript)
  are now loaded from remote urls.

- [#226](https://github.com/zendframework/zend-expressive-skeleton/pull/226)
  renames the factory class `App\Handler\HomePageFactory` to
  `App\Handler\HomePageHandlerFactory` to reflect the name of the class it
  generates.

- [#231](https://github.com/zendframework/zend-expressive-skeleton/pull/231)
  simplifies how zend-servicemanager instances are generated, dropping v2 syntax
  in favor of a configuration-driven v3 syntax.

### Deprecated

- Nothing.

### Removed

- [#183](https://github.com/zendframework/zend-expressive-skeleton/pull/183)
  removes support for PHP versions prior to PHP 7.1.

- The `zend-expressive.programmatic_pipeline` configuration flag is removed from
  `config/autoload/zend-expressive.global.php`, as it is no longer used anywhere.

### Fixed

- [#216](https://github.com/zendframework/zend-expressive-skeleton/pull/216)
  updates the `composer serve command to read `php -S 0.0.0.0:8080 -t public/`.
  This command has been tested to work across a variety of platforms, including
  Windows, macOS, and Linux. However, for Linux users, the command will fail on
  PHP versions prior to 7.1.14 and 7.2.2, due to a language bug. If you are
  using an affected PHP version, you will need to manually start the PHP
  built-in server using the comand `php -S 0.0.0.0:8080 -t public/ public/index.php`.

- [#195](https://github.com/zendframework/zend-expressive-skeleton/pull/195)
  fixes unwanted installation of every dependency when installing a dev version
  of the skeleton.

- [#235](https://github.com/zendframework/zend-expressive-skeleton/pull/235)
  changes the order of pipeline middleware to place the entry for the
  `MethodNotAllowedMiddleware` after both the `ImplicitHeadMiddleware` and
  `ImplicitOptionsMiddleware` entries; this is done to ensure it does not
  intercept HEAD and OPTIONS requests when it should not.

- [#237](https://github.com/zendframework/zend-expressive-skeleton/pull/237)
  adds an exclusion to `.gitignore` for the file `data/cache/.gitkeep`, ensuring
  the directory is checked in to new projects.

## 2.2.0 - 2018-03-12

### Added

- Nothing.

### Changed

- [#239](https://github.com/zendframework/zend-expressive-skeleton/pull/239)
  updates the minimum required version of zend-expressive to 2.2.0.

- [#239](https://github.com/zendframework/zend-expressive-skeleton/pull/239)
  updates the minimum required version of the various zend-expressive-router
  dependencies to 2.2.0.

- [#239](https://github.com/zendframework/zend-expressive-skeleton/pull/239)
  updates the configured middleware pipeline to match requirements of the
  zend-expressive 2.2 release.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.0 - 2017-12-11

### Added

- [#155](https://github.com/zendframework/zend-expressive-skeleton/pull/155)
  adds $app typehinting in routes and pipeline config.

### Changes

- [#160](https://github.com/zendframework/zend-expressive-skeleton/pull/160)
  switches to PSR-11 container references.

- [#153](https://github.com/zendframework/zend-expressive-skeleton/pull/153),
  [#163](https://github.com/zendframework/zend-expressive-skeleton/pull/163)
  simplifies method of checking for static file requests.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#175](https://github.com/zendframework/zend-expressive-skeleton/pull/175)
  adds an authentication fix for fast-cgi.

- [#180](https://github.com/zendframework/zend-expressive-skeleton/pull/180)
  fixes loading config files on IBMi.

## 2.0.4 - 2017-10-12

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#178](https://github.com/zendframework/zend-expressive-skeleton/pull/178)
  fixes the installer to work with the most recent zend-stratigility releases
  by pinning http-interop/http-middleware to `^0.4.1` and
  zendframework/zend-expressive to `^2.0.5`; without these changes, installation
  was leading to exceptions and partial installation previously.

## 2.0.3 - 2017-04-25

### Added

- Nothing.

### Changed

- [#151](https://github.com/zendframework/zend-expressive-skeleton/pull/151)
  updates the following dependencies to use their newly released stable 1.0
  branches:
  - zend-config-aggregator
  - zend-component-installer

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.2 - 2017-04-11

### Added

- Nothing.

### Changes

- Updated select dependencies to latest patch releases:
  - zend-component-installer: 0.7.1 (fixes issue with `ConfigProvider`
    detection)
  - zend-expressive-platesrenderer: 1.3.1 (fixes issue with `UrlExtension`'s
    `$fragmentIdentifier` default value)
  - zend-expressive-tooling: 0.4.1 (brings in `expressive` binary, with its
    `middleware:create` command)

### Deprecated

- Nothing.

### Removed

- [#146](https://github.com/zendframework/zend-expressive-skeleton/pull/146) removes
  obsolete `raise_throwables` key from default config.

### Fixed

- Nothing.

## 2.0.1 - 2017-03-14

### Added

- Nothing.

### Changes

- [#141](https://github.com/zendframework/zend-expressive-skeleton/pull/141) changes the reference
  to the `DefaultDelegate` in `config/autoload/dependencies.global.php` to be a
  string instead of using `::class` notation. Using a string name makes it clear
  the service is not a concrete class or interface name.

- [#143](https://github.com/zendframework/zend-expressive-skeleton/pull/143) updates dependencies
  to pick up the Expressive 2.0.2 release, zend-expressive-helpers 4.0 release,
  and renderer releases related to the helpers 4.0 release.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.0 - 2017-03-07

### Added

- [#54](https://github.com/zendframework/zend-expressive-skeleton/pull/54) adds
  [zfcampus/zf-development-mode](https://github.com/zfcampus/zf-development-mode)
  as a development dependency, and places the application into development mode
  during initial installation. This allows production applications to be
  configured out-of-the-box with features such as configuration caching.

  A new tool, invokable via `composer clear-config-cache`, allows you to clear
  the configuration cache programmatically from the command line if necessary.
  Toggling development mode also clears the configuration cache.

  Development mode commands include:

  - `composer development-enable`
  - `composer development-disable`
  - `composer development-status`
  - `composer clear-config-cache`

- [#124](https://github.com/zendframework/zend-expressive-skeleton/pull/124) adds the ability to
  select the initial application structure from one of the following options:

  - Minimal (no default routes, middleware, or assets)
  - Flat (default routes and assets; `src/` directory is assumed to be library code)
  - Modular (default routes and assets; `src/` directory contains application modules)

  [#138](https://github.com/zendframework/zend-expressive-skeleton/pull/138) updated the logic
  when creating a modular structure to also inject
  [zendframework/zend-expressive-tooling](https://github.com/zendframework/zend-expressive-tooling)
  as a development requirement, as it provides the tools:

  - `./vendor/bin/expressive-module create <modulename>` (create and activate a
    new module in your application, including composer autoloading rules)
  - `./vendor/bin/expressive-module register <modulename>` (register an existing
    module with your application, including composer autoloading rules)
  - `./vendor/bin/expressive-module deregister <modulename>` (deregister an existing
    module from your application, including composer autoloading rules)

### Changes

- [#54](https://github.com/zendframework/zend-expressive-skeleton/pull/54) updates the
  shipped `config/config.php` to leverage [zend-config-aggregator](https://github.com/zendframework/zend-config-aggregator)
  for purposes of aggregating configuration. This change allows the use of
  third party "modules" (packages providing a `ConfigProvider` class that
  returns configuration on invocation) with the skeleton. Additionally, this
  update now adds [zend-component-installer](https://github.com/zendframework/zend-component-installer)
  as a development requirement, which allows packages to declare if they have a
  configuration provider, and then prompt you as to whether or not you want it
  registered in your application.

- [#54](https://github.com/zendframework/zend-expressive-skeleton/pull/54) updates the skeleton to
  default to a _programmatic pipeline_. This results in the following:

  - Removal of the `config/autoload/middleware-pipeline.global.php` file.
  - Addition of a `config/pipeline.php` file, containing the various application
    calls necessary to build your application pipeline; this file may be
    edited to suit your application.
  - Removal of any routing configuration from the `config/autoload/routes.global.php`
    file. Routes are now defined in `config/routes.php` using programmatic
    statements instead. You may add as many routes as you desire to this file,
    segregate them into multiple files, or even add them via delegator factories
    on the `Application` instance.

- [#54](https://github.com/zendframework/zend-expressive-skeleton/pull/54) updates the
  following dependencies:

  - zend-expressive-router to `^2.0`
  - zend-expressive-helpers to `^3.0.1`
  - zend-expressive-aurarouter to `^2.0`
  - zend-expressive-fastroute to `^2.0`
  - zend-expressive-zendrouter to `^2.0`
  - zend-expressive-platesrenderer to `^1.2`
  - zend-expressive-twigrenderer to `^1.2.1`
  - zend-expressive-zendviewrenderer to `^1.2.1`

- [#120](https://github.com/zendframework/zend-expressive-skeleton/pull/120) switches the order of
  questions in the installer, to prompt for the container to use first. This
  will allow some optimizations for some third-party container systems such as
  [Disco](https://github.com/bitExpert/disco).

- [#132](https://github.com/zendframework/zend-expressive-skeleton/pull/132) modifies which file
  the installer writes Whoops configuration to when selected. Previously, it
  wrote it to `config/autoload/local.php`; it now writes it to
  `config/autoload/development.local.php.dist`, allowing enabling/disabling the
  Whoops integration via the `zf-development-mode` tooling.

- [#130](https://github.com/zendframework/zend-expressive-skeleton/pull/130) changes the structure
  of `public/index.php` slightly. In order to prevent creation of new globals,
  it now creates and calls a closure around creation of the container, retrieval
  of the application, registration of the pipeline and routes, and execution of
  the application.

### Deprecated

- Nothing.

### Removed

- [#110](https://github.com/zendframework/zend-expressive-skeleton/pull/110) removes the global
  config array to ArrayObject conversion for all containers except Aura.Di.

### Fixed

- Nothing.

## 1.0.5 - 2017-01-25

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#127](https://github.com/zendframework/zend-expressive-skeleton/pull/127)
  removes PHP 5.5 support.

### Fixed

- [#127](https://github.com/zendframework/zend-expressive-skeleton/pull/127)
  registers the missing TwigEnvironmentFactory which was introduced in
  zend-expressive-twigrenderer 1.2.0. Not having this factory registered
  causes a deprecation message.

## 1.0.4 - 2016-12-01

### Added

- [#113](https://github.com/zendframework/zend-expressive-skeleton/pull/113)
  removes leftover skeleton files.
- [#118](https://github.com/zendframework/zend-expressive-skeleton/pull/118)
  removes CHANGELOG.md, CONDUCT.md and CONTRIBUTING.md after setup.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#114](https://github.com/zendframework/zend-expressive-skeleton/pull/114) fixes
  composer check script.
- [#121](https://github.com/zendframework/zend-expressive-skeleton/pull/121) fixes
  composer serve script for Windows.

## 1.0.3 - 2016-09-01

### Added

- [#93](https://github.com/zendframework/zend-expressive-skeleton/pull/93) adds
  support for Pimple "extensions" (`$pimple->extend()`) via the `dependencies`
  sub-key `extensions`, as follows:

  ```php
  return [
      'dependencies' => [
          'extensions' => [
              SomeClass::class => ExtendingFactory::class,
          ],
      ],
  ];
  ```

- [#93](https://github.com/zendframework/zend-expressive-skeleton/pull/93) adds
  support to the Pimple container script to allow wrapping `delegators`
  (delegator factories from zend-servicemanager) as anonymous Pimple extensions.

### Deprecated

- Nothing.

### Removed

- [#102](https://github.com/zendframework/zend-expressive-skeleton/pull/102)
  removes the development dependendy on ocramius/proxy-manager, as it is not
  required.

### Fixed

- [#91](https://github.com/zendframework/zend-expressive-skeleton/pull/91) fixes
  the Pimple factory caching to work correctly with invokable classes used as
  factories.
- [#95](https://github.com/zendframework/zend-expressive-skeleton/pull/95) fixes
  the prompt for a minimal install to ensure that only `n` and `y` (or uppercase
  versions of each) are valid answers, looping until a valid answer is provided.
- [#101](https://github.com/zendframework/zend-expressive-skeleton/pull/101)
  removes filp/whoops from the `composer.json` prior to prompting the user for
  packages to install, ensuring it does not remain if a user selects a minimal
  install or to not use whoops for development.
- [#109](https://github.com/zendframework/zend-expressive-skeleton/pull/109)
  adds comprehensive, granular tests covering all functionality of the
  installer, raising coverage from 40% to 100%.

## 1.0.2 - 2016-04-21

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#85](https://github.com/zendframework/zend-expressive-skeleton/pull/85)
  updates the Aura.Di dependency to stable 3.X versions.
- [#88](https://github.com/zendframework/zend-expressive-skeleton/pull/88)
  modifies the installer to remove `composer.lock` from the `.gitignore` file
  during initial installation.
- [#89](https://github.com/zendframework/zend-expressive-skeleton/pull/89)
  updates the zend-stdlib dependency to allow usage of its v3 series.

## 1.0.1 - 2016-03-17

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#53](https://github.com/zendframework/zend-expressive-skeleton/pull/53)
  updates the default Pimple container script such that it now caches factory
  instances for re-use.
- [#72](https://github.com/zendframework/zend-expressive-skeleton/pull/72)
  updates the `composer.json` to remove the possibility of installing an
  Expressive RC version, updates zend-servicemanager to allow using 3.0
  versions, and updates whoops to allow either 1.1 or 2.0 versions.
- [#80](https://github.com/zendframework/zend-expressive-skeleton/pull/80)
  updates the default ProxyManager constraints to also allow v2 versions.
- [#81](https://github.com/zendframework/zend-expressive-skeleton/pull/81)
  fixes an issue in the installer whereby specified constraints were not being
  passed to Composer prior to dependency resolution/installation, resulting in
  stale dependencies.
- [#78](https://github.com/zendframework/zend-expressive-skeleton/pull/78)
  updates the shipped default error templates to remove error/exception display.
  Users who really need this functionality can write their own templates; the
  project aims to deliver a "safe by default" setting.

## 1.0.0 - 2016-01-28

First stable release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#69](https://github.com/zendframework/zend-expressive-skeleton/pull/69)
  updates the links in templates to point to the new documentation site on
  https://zendframework.github.io/zend-expressive/ instead of rtfd.org.

## 1.0.0rc8 - 2016-01-21

Eighth release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#66](https://github.com/zendframework/zend-expressive-skeleton/pull/66)
  adds the `'error' => true,` declaration to the `'error'` pipeline middleware
  specification.
- [#67](https://github.com/zendframework/zend-expressive-skeleton/pull/67)
  updates the `filp/whoops` dependency for installer development to `^1.1 || ^2.0`;
  the two are compatible for our use cases, but we should prefer the latest
  that can be installed. As 2.0 requires PHP 5.5.9, but our minimum PHP version
  is 5.5.0, we must specify both.

## 1.0.0rc7 - 2016-01-19

Seventh release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#64](https://github.com/zendframework/zend-expressive-skeleton/pull/64)
  fixes the installer script to correctly rewrite the `require-dev` section
  and ensure only the development dependencies selected, as well as base
  requirements such as PHPUnit and PHP_CodeSniffer, are installed. As such,
  the `--no-dev` flag is no longer required, and development dependencies
  such as whoops are properly installed.

## 1.0.0rc6 - 2016-01-19

Sixth release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#56](https://github.com/zendframework/zend-expressive-skeleton/pull/56)
  updates the `composer serve` command to include the `public/index.php` script
  as an argument. This ensures that asset paths that the application could
  intercept and serve will be passed to the application (previously, the
  built-in server would treat these as 404s, and never pass them to the
  application).
- [#57](https://github.com/zendframework/zend-expressive-skeleton/pull/57)
  updates the Apache configuration rules defined in `public/.htaccess` to omit
  several that could prevent the application from intercepting requests for
  assets.
- [#52](https://github.com/zendframework/zend-expressive-skeleton/pull/52)
  fixes the switch statement in the `HomePageAction` class to ensure the
  template name and documentation link are accurately found.
- [#59](https://github.com/zendframework/zend-expressive-skeleton/pull/59)
  updates the `config/container.php` implementation for zend-servicemanager such
  that it can work with either v2 or v3 of that library.
- [#60](https://github.com/zendframework/zend-expressive-skeleton/pull/60)
  updates the zend-expressive-helpers dependency to `^2.0`, and updates the
  `config/autoload/middleware-pipeline.global.php` to follow the changes in
  middleware configuration introduced in [zend-expressive #270](https://github.com/zendframework/zend-expressive/pull/270).
  The change introduces convention-based keys for "always" (execute before
  routing), "routing" (routing, listeners that act on the route result, and
  dispatching), and "error", with reasonable priorities to ensure execution
  order.
- [#60](https://github.com/zendframework/zend-expressive-skeleton/pull/60)
  fixes the documentation for `composer create-project` to include the
  `--no-dev` flag; this is done as composer currently installs the development
  dependencies listed before the installer script rewrites the `composer.json`
  file. Running `composer update` or `composer install` within the project
  directory after the initial installation will install the development
  dependencies.

## 1.0.0rc5 - 2015-12-22

Fifth release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#42](https://github.com/zendframework/zend-expressive-skeleton/pull/42)
  fixes some grammatical issues in the questions presented by the installer.
- [#45](https://github.com/zendframework/zend-expressive-skeleton/pull/45)
  fixes how JS and CSS assets are added to zend-view templates.
- [#48](https://github.com/zendframework/zend-expressive-skeleton/pull/48)
  adds unit tests for the `OptionalPackages` class (which provides the Composer
  installer scripts).
- [#49](https://github.com/zendframework/zend-expressive-skeleton/pull/49)
  updates the Pimple support to Pimple v3, ensuring Pimple users are using the
  latest stable release.

## 1.0.0rc4 - 2015-12-09

Fourth release candidate.

### Added

- [#34](https://github.com/zendframework/zend-expressive-skeleton/pull/34)
  updates the zend-view configuration to register a factory for
  `Zend\View\HelperPluginManager`, as well as a `view_helpers` sub-key for
  registering custom view helpers.
- [#37](https://github.com/zendframework/zend-expressive-skeleton/pull/37)
  creates the subdirectories `src/App/` and `test/AppTest/`, moving the
  subdirectories of each under those, and updating the `composer.json`
  autoloading directives accordingly. This change will allow new projects to
  implement a "modular" structure if desired, with a subdirectory per namespace.
- [#41](https://github.com/zendframework/zend-expressive-skeleton/pull/41) adds
  the composer script "serve", which fires up the built-in PHP webserver on port
  8080; invoke using `composer serve`.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#23](https://github.com/zendframework/zend-expressive-skeleton/pull/23)
  updates the comment for the glob statements to ensure all 4 (not just 2!)
  possible matches are detailed.
- [#24](https://github.com/zendframework/zend-expressive-skeleton/pull/24)
  updates the `config/config.php` file to store cached configuration as a plain
  PHP file, so that it can simply `include()`; this will be faster than using
  JSON-serialized structures.
- [#30](https://github.com/zendframework/zend-expressive-skeleton/pull/30)
  updates the Twig configuration to follow the changes made for
  [zendframework/zend-expressive-twigrenderer 0.3.0](https://github.com/zendframework/zend-expressive-twigrenderer/releases/tag/0.3.0).
  The old configuration format will still work, though users *should* update
  their configuration to the new format. The change in this patch only affects
  new installs.
- [#33](https://github.com/zendframework/zend-expressive-skeleton/pull/33)
  updates to zendframework/zend-expressive-helpers `^1.2`.
- [#33](https://github.com/zendframework/zend-expressive-skeleton/pull/33) adds
  configuration for auto-registering the new `Zend\Expressive\Helper\UrlHelperMiddleware`
  as pipeline middleware; this fixes an issue when using the zend-view renderer
  with the `url()` helper whereby the `UrlHelper` was being registered as a
  route result observer too late to receive the `RouteResult`.
- [#40](https://github.com/zendframework/zend-expressive-skeleton/pull/40)
  renames the namespace for the installer to `ExpressiveInstaller`.

## 1.0.0rc3 - 2015-12-07

Third release candidate.

### Added

- [#20](https://github.com/zendframework/zend-expressive-skeleton/pull/20) adds
  the ability to specify a "minimal" install; when selected, the installer will
  install modified configuration, omit some files, and remove the default
  middleware and public assets.
- [#27](https://github.com/zendframework/zend-expressive-skeleton/pull/27) adds
  [zendframework/zend-expressive-helpers](https://github.com/zendframework/zend-expressive-helpers)
  as a dependency, and integrates the helpers into the configuration.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#13](https://github.com/zendframework/zend-expressive-skeleton/pull/13)
  updates the installer to also remove the dependency on composer/composer
  on completion.
- [#11](https://github.com/zendframework/zend-expressive-skeleton/pull/11)
  moves the route middleware service definitions into the routes configuration
  files.
- [#21](https://github.com/zendframework/zend-expressive-skeleton/pull/21)
  updates `require` statements in generated configuration files to use the
  `__DIR__` constant to ensure files are located relative to the origin file.
- [#25](https://github.com/zendframework/zend-expressive-skeleton/pull/25) and
  [#29](https://github.com/zendframework/zend-expressive-skeleton/pull/29)
  update minimum versions for each router and template implementation (final
  versions for RC3 are all at `^1.0`).
- [#29](https://github.com/zendframework/zend-expressive-skeleton/pull/29) sets
  the zend-expressive required version to `~1.0.0@rc || ^1.0`, to ensure a
  stable version is always installed.

## 1.0.0rc2 - 2015-10-20

Second release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updated expressive to RC2.
- Updated subcomponent versions in installer to `^0.2`

## 1.0.0rc1 - 2015-10-19

First release candidate.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.5.3 - 2015-10-16

### Added

- [#8](https://github.com/zendframework/zend-expressive-skeleton/pull/8) adds a
  routine to the installer that recursively removes the `src/Composer/`
  directory of the skeleton, ensuring you have a clean start when creating a
  project.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.5.2 - 2015-10-13

### Added

- [#7](https://github.com/zendframework/zend-expressive-skeleton/pull/7) adds a
  dependency on zend-stdlib for the purposes of globbing and merging
  configuration.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.5.1 - 2015-10-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#6](https://github.com/zendframework/zend-expressive-skeleton/pull/6) updates
  the zendframework/zend-view package configuration to remove the dependency on
  zendframework/zend-i18n, as it is now handled in the standalone
  zend-expressive-zendviewrenderer package.

## 0.5.0 - 2015-10-10

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#3](https://github.com/zendframework/zend-expressive-skeleton/pull/3) updates
  the skeleton to use zendframework/zend-expressive 0.4.0.

## 0.4.0 - 2015-10-09

First release as zend-expressive-skeleton.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2015-09-12

### Added

- Use zend-expressive template factories.
- Use the zend view url helper in the layout template.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0 - 2015-09-11

### Added

- [#bbb2e60](https://github.com/xtreamwayz/expressive-composer-installer/commit/bbb2e607af23e3ae23f6a9c71eb97c3c651c0ca1) adds PHPUnit tests.
- [#791c1c6](https://github.com/xtreamwayz/expressive-composer-installer/commit/791c1c63f324ca08d08e26375f3a356102bf2ad9) adds Whoops error handler.
- [e1d8d7bf](https://github.com/xtreamwayz/expressive-composer-installer/commit/e1d8d7bf5d5e2f51863fa59a37d1963405743201) adds config caching in production mode.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.1 - 2015-09-08

### Added

- [#b4a0923](https://github.com/xtreamwayz/expressive-composer-installer/commit/b4a092386993227f8057d7ad4e0d9762659eefb0) adds support for Pimple 3.0.x. Still needs testing!

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#11](https://github.com/xtreamwayz/expressive-composer-installer/issues/11) fixes an issues where non stable packages are not being installed correctly.

## 0.1.0 - 2015-09-07

Initial tagged release.

### Added

- Everything.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
