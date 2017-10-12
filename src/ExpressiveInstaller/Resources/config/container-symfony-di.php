<?php

use App\ExpressiveSymfonyContainer;

require_once __DIR__ . '/CallableFactory.php';
require_once __DIR__ . '/ExpressiveSymfonyContainer.php';
require_once __DIR__ . '/ExpressiveSymfonyDelegatorFactory.php';

// Load configuration
$config = require __DIR__ . '/config.php';

$cacheContainerPath = __DIR__ . '/../data/symfony-container-cache.php';

return (new ExpressiveSymfonyContainer($config, $cacheContainerPath))->create();
