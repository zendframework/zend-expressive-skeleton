<?php

namespace App;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return Container
     */
    public function build()
    {
        // Build container
        $container = new ContainerBuilder();

        // Inject config
        $container->register('config')->setSynthetic(true);
        $container->set('config', $this->config);

        // Inject delegator factories
        // This is done early because Symfony Dependency Injection does not allow modification of a
        // service after creation. As such, we need to create custom factories
        // for each service with delegators.
        if (! empty($this->config['dependencies']['delegators'])
            && is_array($this->config['dependencies']['delegators'])
        ) {
            $this->marshalDelegators($container);
        }

        // Inject services
        if (! empty($this->config['dependencies']['services'])
            && is_array($this->config['dependencies']['services'])
        ) {
            foreach ($this->config['dependencies']['services'] as $name => $service) {
                $container->register($name)->setSynthetic(true);
                $container->set($name, $service);
            }
        }

        // Inject factories
        foreach ($this->config['dependencies']['factories'] as $name => $object) {
            $container->register($name)
                ->addArgument($container)
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
                ->addArgument($container)
                ->addArgument($delegatorFactory)
                ->addArgument($service)
                ->setClass($service)
                ->setFactory([\App\CallableFactory::class, 'build']);
        }
    }
}
