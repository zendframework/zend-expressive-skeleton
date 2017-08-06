<?php

/**
 * Set-up Doctrine command line.
 *
 * Usage: vendor/bin/doctrine <command>
 */

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$container = require_once __DIR__ . '/container.php';

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
