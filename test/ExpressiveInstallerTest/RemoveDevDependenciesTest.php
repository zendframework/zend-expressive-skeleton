<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

namespace ExpressiveInstallerTest;

use ExpressiveInstaller\OptionalPackages;

class RemoveDevDependenciesTest extends InstallerTestCase
{
    private $standardDependencies = [
        'php',
        'roave/security-advisories',
        'zendframework/zend-expressive',
        'zendframework/zend-expressive-helpers',
        'zendframework/zend-stdlib',
        'phpunit/phpunit',
        'zendframework/zend-coding-standard',
    ];

    private $devDependencies      = [
        'aura/di',
        'composer/composer',
        'filp/whoops',
        'xtreamwayz/pimple-container-interop',
        'zendframework/zend-expressive-aurarouter',
        'zendframework/zend-expressive-fastroute',
        'zendframework/zend-expressive-platesrenderer',
        'zendframework/zend-expressive-twigrenderer',
        'zendframework/zend-expressive-zendrouter',
        'zendframework/zend-expressive-zendviewrenderer',
        'zendframework/zend-servicemanager',
    ];

    public function testComposerHasAllDependencies()
    {
        $this->assertComposerHasPackages($this->standardDependencies);
        $this->assertComposerHasPackages($this->devDependencies);
    }

    public function testDevDependenciesAreRemoved()
    {
        // Prepare the installer
        OptionalPackages::removeDevDependencies();

        $this->assertComposerHasPackages($this->standardDependencies);
        $this->assertComposerNotHasPackages($this->devDependencies);
    }
}
