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

    public function __construct($factory)
    {
        if (is_string($factory) && class_exists($factory)) {
            $factory = new $factory;
        }

        $this->factory = $factory;
    }

    public function __invoke(ContainerInterface $container)
    {
        return ($this->factory)($container);
    }
}
