<?php

declare(strict_types=1);

use Northwoods\Container\Zend\ContainerFactory;
use Northwoods\Container\Zend\Config;

$config = new Config(require __DIR__ . '/config.php');
$factory = new ContainerFactory();

return $factory($config);
