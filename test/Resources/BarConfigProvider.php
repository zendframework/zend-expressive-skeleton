<?php

namespace AppTest\Resources;

use App\ConfigProviderInterface;
use ArrayObject;

class BarConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig()
    {
        return ['bar' => 'bat'];
    }
}
