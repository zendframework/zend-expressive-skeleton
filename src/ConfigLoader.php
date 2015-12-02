<?php
namespace App;

use ArrayObject;
use RuntimeException;
use Zend\Stdlib\ArrayUtils;

class ConfigLoader
{
    /**
     * @var ArrayObject
     */
    private $config;

    private function loadConfigFromProviders(array $providers)
    {
        $config = [];
        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                throw new RuntimeException("Cannot read config from $providerClass - class cannot be loaded.");
            }
            $provider = new $providerClass();
            if (!$provider instanceof ConfigProviderInterface) {
                throw new RuntimeException("Cannot read config from $providerClass - class does not implement ConfigProviderInterface");
            }
            $config = ArrayUtils::merge($config, $provider->getConfig());
        }
        return $config;
    }

    private function loadConfigFromFiles(array $configFiles)
    {
        $config = [];
        // Load configuration from autoload path
        foreach ($configFiles as $file) {
            echo $file, "\n";
            $config = ArrayUtils::merge($config, include $file);
        }
        return $config;
    }

    public function __construct(array $configFiles, array $providers, $cachedConfigFile = 'data/cache/app_config.php')
    {
        if (is_file($cachedConfigFile)) {
            // Try to load the cached config
            $this->config = json_decode(file_get_contents($cachedConfigFile), true);
            return;
        }

        $config = ArrayUtils::merge(
            $this->loadConfigFromProviders($providers),
            $this->loadConfigFromFiles($configFiles)
        );

        // Cache config if enabled
        if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
            file_put_contents($cachedConfigFile, json_encode($config));
        }

        // Return an ArrayObject so we can inject the config as a service in Aura.Di
        // and still use array checks like ``is_array``.
        $this->config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return ArrayObject
     */
    public function getMergedConfig()
    {
        return $this->config;
    }
}
