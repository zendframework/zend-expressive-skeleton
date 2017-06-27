<?php

declare(strict_types=1);

namespace App;

use Interop\Container\ContainerInterface;

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
