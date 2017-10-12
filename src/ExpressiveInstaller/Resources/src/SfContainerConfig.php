<?php
namespace App;

use ArrayObject;
use JSoumelidis\ZendSmSfDiBridge\ZendSmSfDiBridge;
use JSoumelidis\SfContainerInterop\SfContainerInterop;
use JSoumelidis\SfContainerInterop\SfContainerBuilderInterop;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SfContainerConfig
{
    /**
     * boolean: toggle container caching
     */
    const ENABLE_CACHE = 'sf-container.enable_cache';

    /**
     * string: A valid class name to use for the ContainerBuilder
     */
    const BUILDER_CLASS = 'sf-container.builder_class';

    /**
     * string: path to container's cache file
     */
    const CACHE_FILE = 'sf-container.cache_file';

    /**
     * string: A valid class name for the cached container
     */
    const CACHE_CLASS = 'sf-container.cache_class';

    /**
     * string: A valid namespace for the cached container
     */
    const CACHE_CLASS_NS = 'sf-container.cache_class_ns';

    /*
     * string: An existing class name that the cached container should extend
     */
    const CACHE_BASE_CLASS = 'sf-container.cache_base_class';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $enableCache = false;

    /**
     * @var string
     */
    protected $builderClass = SfContainerBuilderInterop::class;

    /**
     * @var string
     */
    protected $cacheFile = 'data/sf-container-cache.php';

    /**
     * @var string
     */
    protected $cacheClass = 'SfCachedContainer';

    /**
     * @var string
     */
    protected $cacheClassNs = '';

    /**
     * @var string
     */
    protected $cacheBaseClass = SfContainerInterop::class;

    /**
     * SfContainerConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        //Register 'config' service
        //As config may alter between requests (if config cache is not enabled),
        //it will be registered by the bridge as a synthetic service
        if (! isset($config['dependencies']['services']['config'])) {
            $config['dependencies']['services']['config'] = new ArrayObject($config);
        }

        $this->config = $config;

        $this->enableCache = (isset($config[static::ENABLE_CACHE]) && $config[static::ENABLE_CACHE]);

        if (isset($config[static::BUILDER_CLASS]) && class_exists($config[static::BUILDER_CLASS])) {
            $this->builderClass = $config[static::BUILDER_CLASS];
        }

        if (isset($config[static::CACHE_FILE])) {
            $this->cacheFile = $config[static::CACHE_FILE];
        }

        if (isset($config[static::CACHE_CLASS])) {
            $this->cacheClass = $config[static::CACHE_CLASS];
        }

        if (isset($config[static::CACHE_CLASS_NS])) {
            $this->cacheClassNs = $config[static::CACHE_CLASS_NS];
        }

        if (isset($config[static::CACHE_BASE_CLASS])) {
            $this->cacheBaseClass = $config[static::CACHE_BASE_CLASS];
        }
    }

    /**
     * @return Container
     */
    public function create()
    {
        if (! $this->enableCache || ! is_readable($this->cacheFile)) {
            $container = $this->build($this->enableCache);
        } else {
            $container = $this->loadFromCache();
        }

        //Set known (synthetic) services
        if (! empty($this->config['dependencies']['services'])
        &&   is_array($this->config['dependencies']['services'])) {
            foreach ($this->config['dependencies']['services'] as $name => $object) {
                $container->set($name, $object);
            }
        }

        return $container;
    }

    /**
     * @param bool $dump
     *
     * @return ContainerBuilder
     */
    protected function build($dump = false)
    {
        $config = $this->config;

        $dependencies = isset($config['dependencies']) ? $this->config['dependencies'] : [];
        unset($config['dependencies']);

        $builderClass = $this->builderClass;

        $container = (new ZendSmSfDiBridge(true))->toSfDI(
            $dependencies,
            new $builderClass(new ParameterBag($config))
        );

        //Inject any additional symfony di definitions provided
        if (! empty($dependencies['definitions']) && is_array($dependencies['definitions'])) {
            $container->addDefinitions($dependencies['definitions']);
        }

        if ($dump) {
            //Container must be compiled before dumped to a file
            $container->compile();

            $dumper = new PhpDumper($container);

            file_put_contents($this->cacheFile, $dumper->dump([
                'class' => $this->cacheClass,
                'namespace' => $this->cacheClassNs,
                'base_class' => $this->cacheBaseClass,
                //use expressive 'debug' setting
                'debug' => isset($this->config['debug']) && $this->config['debug'],
            ]));
        }

        return $container;
    }

    /**
     * @return Container
     */
    protected function loadFromCache()
    {
        require_once($this->cacheFile);

        $class = ! empty($this->cacheClassNs) ?
            $this->cacheClassNs . '\\' . $this->cacheClass :
            $this->cacheClass;

        return new $class;
    }
}
