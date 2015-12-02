<?php

namespace AppTest\Resources;

use App\ConfigProviderInterface;
use ArrayObject;

class FooConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig()
    {
        return ['foo' => 'bar'];
    }
}
