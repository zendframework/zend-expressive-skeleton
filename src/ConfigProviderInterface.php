<?php

namespace App;

use ArrayObject;

interface ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig();
}
