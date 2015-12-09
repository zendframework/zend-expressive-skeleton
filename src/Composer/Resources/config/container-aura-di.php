<?php

use Aura\Di\ContainerBuilder;

// Load configuration
$config = require __DIR__ . '/config.php';

// Build container
$builder = new ContainerBuilder();
$container = $builder->newInstance();

// Add config classes for two stage configuration
// http://auraphp.com/blog/2014/04/07/two-stage-config/
$configClasses = [
    // 'Common',
    // 'Dev',
    // 'Prod',
];
$configs = [];
foreach ($configClasses as $configClass) {
    $conf = $container->newInstance($configClass);
    $conf->define($container);
    $configs[] = $conf;
}

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

// Lock the container
$container->lock();

foreach ($configs as $conf) {
    $conf->modify($container);
}
return $container;
