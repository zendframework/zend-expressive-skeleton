<?php

use Interop\Container\Pimple\PimpleInterop;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$container = new PimpleInterop();

// Inject config
$container['config'] = $config;

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $container[$name] = $container->share(function ($c) use ($object) {
        $factory = new $object();
        return $factory($c);
    });
}

// Inject invokables
foreach ($config['dependencies']['invokables'] as $name => $object) {
    $container[$name] = $container->share(function ($c) use ($object) {
        return new $object();
    });
}

return $container;
