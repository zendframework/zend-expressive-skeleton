<?php

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Symfony Dependency Injection compatible delegator factory
 *
 * Instances receive the list of delegator factory names or instances, and a
 * closure that can create the initial service instance to pass to the first
 * delegator.
 */
class ExpressiveSymfonyDelegatorFactory
{
    /**
     * @var array Either delegator factory names or instances.
     */
    private $delegators;

    /**
     * @var callable
     */
    private $factory;

    /**
     * @param array $delegators Array of delegator factory names or instances.
     * @param callable $factory Callable that can return the initial instance.
     */
    public function __construct(array $delegators, callable $factory)
    {
        $this->delegators = $delegators;
        $this->factory    = $factory;
    }

    /**
     * Build the instance, invoking each delegator with the result of the previous.
     *
     * @param ContainerBuilder $container
     * @param string $serviceName
     * @return mixed
     */
    public function __invoke(ContainerBuilder $container, $serviceName)
    {
        $factory = $this->factory;
        return array_reduce(
            $this->delegators,
            function ($instance, $delegatorName) use ($serviceName, $container) {
                $delegator = is_callable($delegatorName) ? $delegatorName : new $delegatorName();
                return $delegator($container, $serviceName, function () use ($instance) {
                    return $instance;
                });
            },
            $factory()
        );
    }
}
