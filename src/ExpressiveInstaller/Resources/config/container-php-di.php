<?php

declare(strict_types = 1);

use Zend\DI\Config\Config;
use Zend\DI\Config\ContainerFactory;

$config  = require __DIR__ . '/config.php';
$factory = new ContainerFactory();

return $factory(new Config($config));
