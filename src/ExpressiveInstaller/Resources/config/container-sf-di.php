<?php
use App\SfContainerConfig;

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'SfContainerConfig.php');

// Load configuration
$config = require __DIR__ . '/config.php';

return (new SfContainerConfig($config))->create();
