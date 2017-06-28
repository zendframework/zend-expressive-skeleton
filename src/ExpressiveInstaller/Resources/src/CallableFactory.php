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
     * @param ContainerInterface $container
     * @param callable|string $factory
     * @param string $serviceName
     * @return mixed
     */
    public static function build(ContainerInterface $container, $factory, $serviceName)
    {
        if (is_string($factory) && class_exists($factory)) {
            $factory = new $factory;
        }

        return call_user_func($factory, $container, $serviceName);
    }
}
