<?php

use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\GlobFileProvider;

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
        new GlobFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    ]
);

return $configManager->getMergedConfig();

