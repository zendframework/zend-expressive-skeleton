<?php

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new Container();

// Inject config
$container['config'] = $config;

// Inject services
if (! empty($config['dependencies']['services'])
    && is_array($config['dependencies']['services'])
) {
    foreach ($config['dependencies']['services'] as $name => $service) {
        $container[$name] = function (Container $c) use ($service) {
            return $service;
        };
    }
}

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $container[$name] = function (Container $c) use ($object, $name) {
        $psrContainer = new PsrContainer($c);

        if ($psrContainer->has($object)) {
            $factory = $psrContainer->get($object);
        } else {
            $factory = new $object();
            $container[$object] = $c->protect($factory);
        }

        return $factory(new PsrContainer($c), $name);
    };
}

// Inject invokables
foreach ($config['dependencies']['invokables'] as $name => $object) {
    $container[$name] = function (Container $c) use ($object) {
        return new $object();
    };
}

// Inject aliases
foreach ($config['dependencies']['aliases'] as $alias => $target) {
    $container[$alias] = function (Container $c) use ($target) {
        $psrContainer = new PsrContainer($c);

        return $psrContainer->get($target);
    };
}

// Inject "pimple extend-style" factories
if (! empty($config['dependencies']['extensions'])
    && is_array($config['dependencies']['extensions'])
) {
    foreach ($config['dependencies']['extensions'] as $name => $extensions) {
        foreach ($extensions as $extension) {
            $container->extend($name, function ($service, $c) use ($extension, $name) {
                $factory = new $extension();
                return $factory($service, $c, $name); // passing extra parameter $name
            });
        }
    }
}

// Inject "zend-servicemanager3 style" delegators as Pimple anonymous "extend" functions
if (! empty($config['dependencies']['delegators'])
    && is_array($config['dependencies']['delegators'])
) {
    foreach ($config['dependencies']['delegators'] as $name => $delegators) {
        foreach ($delegators as $delegator) {
            $container->extend($name, function ($service, $c) use ($delegator, $name) {
                $factory  = new $delegator();
                $callback = function () use ($service) {
                    return $service;
                };

                return $factory($c, $name, $callback);
            });
        }
    }
}

return new PsrContainer($container);
