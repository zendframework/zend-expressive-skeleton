<?php

use App\ApplicationConfig;
use App\ConfigLoader;
use Zend\Stdlib\Glob;

/**
 * Configuration files are loaded in a specific order. First ``global.php`` and afterwards ``local.php``. This way
 * local settings overwrite global settings.
 *
 * The configuration can be cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 *
 * Obviously, if you use closures in your config you can't cache it.
 */

$moduleManager = new ConfigLoader(
    Glob::glob('config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE),
    [
        ApplicationConfig::class
    ]
);

return $moduleManager->getMergedConfig();
