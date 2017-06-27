<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once __DIR__ . '/CallableFactory.php';
require_once __DIR__ . '/ExpressiveSymfonyDelegatorFactory.php';

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new class() extends ContainerBuilder implements \Interop\Container\ContainerInterface {};

// Inject config
$container->register('config')->setSynthetic(true);
$container->set('config', $config);

// Inject delegator factories
// This is done early because Symfony Dependency Injection does not allow modification of a
// service after creation. As such, we need to create custom factories
// for each service with delegators.
if (! empty($config['dependencies']['delegators'])
    && is_array($config['dependencies']['delegators'])
) {
    foreach ($config['dependencies']['delegators'] as $service => $delegatorNames) {
        $factory = null;

        if (isset($config['dependencies']['services'][$service])) {
            // Marshal from service
            $instance = $config['dependencies']['services'][$service];
            $factory = function () use ($instance) {
                return $instance;
            };
            unset($config['dependencies']['services'][$service]);
        }

        if (isset($config['dependencies']['factories'][$service])) {
            // Marshal from factory
            $serviceFactory = $config['dependencies']['factories'][$service];
            $factory = function () use ($service, $serviceFactory, $container) {
                if (is_string($serviceFactory) && class_exists($serviceFactory)) {
                    $serviceFactory = new $serviceFactory;
                }

                return $serviceFactory($container, $service);
            };
            unset($config['dependencies']['factories'][$service]);
        }

        if (isset($config['dependencies']['invokables'][$service])) {
            // Marshal from invokable
            $class = $config['dependencies']['invokables'][$service];
            $factory = function () use ($class) {
                return new $class();
            };
            unset($config['dependencies']['invokables'][$service]);
        }

        if (!is_callable($factory)) {
            continue;
        }

        $delegatorFactory = new \App\ExpressiveSymfonyDelegatorFactory($delegatorNames, $factory);
        $container->register($service)
            ->addArgument($container)
            ->setClass($service)
            ->setFactory([new \App\CallableFactory($delegatorFactory, $service), '__invoke']);
    }
}

// Inject services
if (! empty($config['dependencies']['services'])
    && is_array($config['dependencies']['services'])
) {
    foreach ($config['dependencies']['services'] as $name => $service) {
        $container->register($name)->setSynthetic(true);
        $container->set($name, $service);
    }
}

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $container->register($name)
        ->addArgument($container)
        ->setFactory([new \App\CallableFactory($object, $name), '__invoke']);
}

// Inject invokables
foreach ($config['dependencies']['invokables'] as $name => $object) {
    $container->register($name, new $object);
}

// Inject aliases
foreach ($config['dependencies']['aliases'] as $alias => $target) {
    $container->setAlias($alias, $target);
}

$container->compile();

return $container;
