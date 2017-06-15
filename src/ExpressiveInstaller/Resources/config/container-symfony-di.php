<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once __DIR__ . '/CallableFactory.php';

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new class() extends ContainerBuilder implements \Interop\Container\ContainerInterface {};

// Inject config
$container->register('config', new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS));

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
            unset($config['dependencies']['service'][$service]);
        }

        if (isset($config['dependencies']['factories'][$service])) {
            // Marshal from factory
            $serviceFactory = $config['dependencies']['factories'][$service];
            $factory = function () use ($service, $serviceFactory, $container) {
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

        $container->register($service)
            ->addArgument($container)
            ->setFactory([new \App\CallableFactory($factory), '__invoke']);
    }
}

// Inject services
if (! empty($config['dependencies']['services'])
    && is_array($config['dependencies']['services'])
) {
    foreach ($config['dependencies']['services'] as $name => $service) {
        $container->register($name, $service);
    }
}

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $container->register($name)
        ->addArgument($container)
        ->setFactory([new \App\CallableFactory($object), '__invoke']);
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
