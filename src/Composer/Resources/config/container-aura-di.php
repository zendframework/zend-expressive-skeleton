<?php

use Aura\Di\ContainerBuilder;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$builder = new ContainerBuilder();
$container = $builder->newInstance();

// Inject config
$container->set('config', $config);

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $container->set($object, $container->lazyNew($object));
    $container->set($name, $container->lazyGetCall($object, '__invoke', $container));
}

// Inject invokables
foreach ($config['dependencies']['invokables'] as $name => $object) {
    $container->set($name, $container->lazyNew($object));
}

return $container;
