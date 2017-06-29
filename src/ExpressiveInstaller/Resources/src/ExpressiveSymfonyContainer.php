<?php

namespace App;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configuration for the Symfony Dependency Injection container.
 *
 * This class provides functionality for the following service types:
 *
 * - Aliases
 * - Delegators
 * - Factories
 * - Invokable classes
 * - Services (known instances)
 */
class ExpressiveSymfonyContainer
{
    const ENABLE_CACHE = 'cache_symfony_container';

    /**
     * @var array
     */
    private $config;

    /**
     * @var string|null
     */
    private $cachedContainerFile;

    /**
     * @param array $config
     */
    public function __construct(
        array $config,
        $cachedContainerFile = null
    ) {
        $this->config = $config;
        $this->cachedContainerFile = $cachedContainerFile;
    }

    /**
     * @return Container
     */
    public function create()
    {
        $loadFromCache = null !== $this->cachedContainerFile &&
            file_exists($this->cachedContainerFile) &&
            isset($this->config[static::ENABLE_CACHE]) &&
            $this->config[static::ENABLE_CACHE];

        if ($loadFromCache) {
            $container = $this->loadFromCache($this->cachedContainerFile);
        } else {
            $container = $this->cacheContainer($this->build());
        }

        // Inject config
        $container->set('config', $this->config);

        // Inject services
        if (! empty($this->config['dependencies']['services'])
            && is_array($this->config['dependencies']['services'])
        ) {
            foreach ($this->config['dependencies']['services'] as $name => $service) {
                $container->set($name, $service);
            }
        }

        return $container;
    }

    /**
     * @var string $cachedContainerFile
     * @return \ProjectServiceContainer
     */
    private function loadFromCache($cachedContainerFile)
    {
        require_once $cachedContainerFile;

        return new \ProjectServiceContainer();
    }

    /**
     * @return ContainerBuilder
     */
    private function build()
    {
        // Build container
        $container = new ContainerBuilder();

        // Inject delegator factories
        // This is done early because Symfony Dependency Injection does not allow modification of a
        // service after creation. As such, we need to create custom factories
        // for each service with delegators.
        if (! empty($this->config['dependencies']['delegators'])
            && is_array($this->config['dependencies']['delegators'])
        ) {
            $this->marshalDelegators($container);
        }

        // Inject factories
        foreach ($this->config['dependencies']['factories'] as $name => $object) {
            $container->register($name)
                ->setClass($object)
                ->addArgument(new Reference('service_container'))
                ->addArgument($object)
                ->addArgument($name)
                ->setFactory([\App\CallableFactory::class, 'build']);
        }

        // Inject invokables
        foreach ($this->config['dependencies']['invokables'] as $name => $object) {
            $container->register($name, $object);
        }

        // Inject aliases
        foreach ($this->config['dependencies']['aliases'] as $alias => $target) {
            $container->setAlias($alias, $target);
        }

        $container->compile();

        return $container;
    }

    private function marshalDelegators(ContainerBuilder $container)
    {
        foreach ($this->config['dependencies']['delegators'] as $service => $delegatorNames) {
            $factory = null;

            if (isset($this->config['dependencies']['services'][$service])) {
                // Marshal from service
                $instance = $this->config['dependencies']['services'][$service];
                $factory = function () use ($instance) {
                    return $instance;
                };
                unset($this->config['dependencies']['services'][$service]);
            }

            if (isset($this->config['dependencies']['factories'][$service])) {
                // Marshal from factory
                $serviceFactory = $this->config['dependencies']['factories'][$service];
                $factory = function () use ($service, $serviceFactory, $container) {
                    if (is_string($serviceFactory) && class_exists($serviceFactory)) {
                        $serviceFactory = new $serviceFactory;
                    }

                    return $serviceFactory($container, $service);
                };
                unset($this->config['dependencies']['factories'][$service]);
            }

            if (isset($this->config['dependencies']['invokables'][$service])) {
                // Marshal from invokable
                $class = $this->config['dependencies']['invokables'][$service];
                $factory = function () use ($class) {
                    return new $class();
                };
                unset($this->config['dependencies']['invokables'][$service]);
            }

            if (! is_callable($factory)) {
                continue;
            }

            $delegatorFactory = new \App\ExpressiveSymfonyDelegatorFactory($delegatorNames, $factory);
            $container->register($service)
                ->addArgument(new Reference('service_container'))
                ->addArgument($delegatorFactory)
                ->addArgument($service)
                ->setClass($service)
                ->setFactory([\App\CallableFactory::class, 'build']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return Container
     */
    private function cacheContainer(ContainerBuilder $container)
    {
        $cacheContainer = (null !== $this->cachedContainerFile) &&
            isset($this->config[static::ENABLE_CACHE]) &&
            $this->config[static::ENABLE_CACHE];

        if ($cacheContainer) {
            $dumper = new PhpDumper($container);

            $container = $dumper->dump();

            file_put_contents($this->cachedContainerFile, $container);
        }

        return $container;
    }
}
