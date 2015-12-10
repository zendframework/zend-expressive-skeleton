<?php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\PhpFileProvider;

/**
 * Configuration files are loaded in a specific order. First ``global.php``, then ``*.global.php``.
 * then ``local.php`` and finally ``*.local.php``. This way local settings overwrite global settings.
 *
 * The configuration can be cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 *
 * Obviously, if you use closures in your config you can't cache it.
 */

$configManager = new ConfigManager(
    [
        new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    ]
);

// Return an ArrayObject so we can inject the config as a service in Aura.Di
// and still use array checks like ``is_array``.
return new ArrayObject($configManager->getMergedConfig());

