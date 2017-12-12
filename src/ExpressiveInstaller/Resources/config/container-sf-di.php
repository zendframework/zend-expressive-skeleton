<?php

use JSoumelidis\SymfonyDI\Config\Config;
use JSoumelidis\SymfonyDI\Config\ContainerFactory;

$config  = require realpath(__DIR__) . '/config.php';
$factory = new ContainerFactory();

return $factory(new Config($config));
