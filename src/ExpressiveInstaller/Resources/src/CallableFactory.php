<?php

namespace App;

use Psr\Container\ContainerInterface;

/**
 * Symfony dependency injection compatible factory
 *
 * Turns any callable with the signature
 *
 * (ContainerInterface $container, string $serviceName) -> mixed $serviceInstance
 *
 * into a factory compatible with Symfony Dependency Injection
 */
final class CallableFactory
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * CallableFactory constructor.
     * @param string|callable $factory
     * @param string $serviceName
     */
    public function __construct($factory, $serviceName)
    {
        if (is_string($factory) && class_exists($factory)) {
            $factory = new $factory;
        }

        $this->factory = $factory;
        $this->serviceName = $serviceName;
    }

    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        return ($this->factory)($container, $this->serviceName);
    }
}
