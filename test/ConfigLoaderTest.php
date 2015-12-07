<?php

namespace AppTest;

use App\ConfigLoader;
use AppTest\Resources\BarConfigProvider;
use AppTest\Resources\FooConfigProvider;
use PHPUnit_Framework_TestCase;
use Zend\Stdlib\Glob;

class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testConfigLoaderMergesConfigFromProviders()
    {
        $loader = new ConfigLoader([], [FooConfigProvider::class, BarConfigProvider::class]);
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bat'], (array)$config);
    }

    public function testConfigLoaderMergesConfigFromFiles()
    {
        $loader = new ConfigLoader(Glob::glob('test/Resources/config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE), []);
        $config = $loader->getMergedConfig();
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], (array)$config);
    }
}
