<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;

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
