<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

namespace ExpressiveInstallerTest;

use Aura\Di\Container as AuraContainer;
use ExpressiveInstaller\OptionalPackages;
use Psr\Container\ContainerInterface;
use Xtreamwayz\Pimple\Container as PimpleContainer;
use Zend\Expressive;
use Zend\ServiceManager\ServiceManager as ZendServiceManagerContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;

class ContainersTest extends OptionalPackagesTestCase
{
    use ProjectSandboxTrait;

    /**
     * @var OptionalPackages
     */
    private $installer;

    protected function setUp()
    {
        parent::setUp();
        $this->projectRoot = $this->copyProjectFilesToTempFilesystem();
        $this->installer   = $this->createOptionalPackages($this->projectRoot);
    }

    protected function tearDown()
    {
        parent::tearDown();
        chdir($this->packageRoot);
        $this->recursiveDelete($this->projectRoot);
        $this->tearDownAlternateAutoloader();
    }

    /**
     * @runInSeparateProcess
     *
     * @dataProvider containerProvider
     *
     * @param string $installType
     * @param int $containerOption
     * @param int $routerOption
     * @param string $copyFilesKey
     * @param int $expectedResponseStatusCode
     * @param string $expectedContainer
     */
    public function testContainer(
        $installType,
        $containerOption,
        $routerOption,
        $copyFilesKey,
        $expectedResponseStatusCode,
        $expectedContainer
    ) {
        $this->prepareSandboxForInstallType($installType, $this->installer);

        // Install container
        $config = $this->getInstallerConfig($this->installer);
        $containerResult = $this->installer->processAnswer(
            $config['questions']['container'],
            $containerOption
        );
        $this->assertTrue($containerResult);

        // Install router
        $routerResult = $this->installer->processAnswer(
            $config['questions']['router'],
            $routerOption
        );
        $this->assertTrue($routerResult);

        // Test container
        $container = $this->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertInstanceOf($expectedContainer, $container);
        $this->assertTrue($container->has(Expressive\Helper\UrlHelper::class));
        $this->assertTrue($container->has(Expressive\Helper\ServerUrlHelper::class));
        $this->assertTrue($container->has(Expressive\Application::class));
        $this->assertTrue($container->has(Expressive\Router\RouterInterface::class));

        // Test home page
        $setupRoutes = strpos($copyFilesKey, 'minimal') !== 0;
        $response = $this->getAppResponse('/', $setupRoutes);
        $this->assertEquals($expectedResponseStatusCode, $response->getStatusCode());
    }

    public function containerProvider()
    {
        // @codingStandardsIgnoreStart
        // $installType, $containerOption, $routerOption, $copyFilesKey, $expectedResponseStatusCode, $expectedContainer
        return [
            'aura-minimal'    => [OptionalPackages::INSTALL_MINIMAL, 1, 2, 'minimal-files', 404, AuraContainer::class],
            'aura-flat'       => [OptionalPackages::INSTALL_FLAT,    1, 2, 'copy-files', 200, AuraContainer::class],
            'aura-modular'    => [OptionalPackages::INSTALL_MODULAR, 1, 2, 'copy-files', 200, AuraContainer::class],
            'pimple-minimal'  => [OptionalPackages::INSTALL_MINIMAL, 2, 2, 'minimal-files', 404, PimpleContainer::class],
            'pimple-flat'     => [OptionalPackages::INSTALL_FLAT,    2, 2, 'copy-files', 200, PimpleContainer::class],
            'pimple-modular'  => [OptionalPackages::INSTALL_MODULAR, 2, 2, 'copy-files', 200, PimpleContainer::class],
            'zend-sm-minimal' => [OptionalPackages::INSTALL_MINIMAL, 3, 2, 'minimal-files', 404, ZendServiceManagerContainer::class],
            'zend-sm-flat'    => [OptionalPackages::INSTALL_FLAT,    3, 2, 'copy-files', 200, ZendServiceManagerContainer::class],
            'zend-sm-modular' => [OptionalPackages::INSTALL_MODULAR, 3, 2, 'copy-files', 200, ZendServiceManagerContainer::class],
            'sf-di-minimal'   => [OptionalPackages::INSTALL_MINIMAL, 4, 2, 'minimal-files', 404, SfContainerBuilder::class],
            'sf-di-flat'      => [OptionalPackages::INSTALL_FLAT,    4, 2, 'copy-files', 200, SfContainerBuilder::class],
            'sf-di-modular'   => [OptionalPackages::INSTALL_MODULAR, 4, 2, 'copy-files', 200, SfContainerBuilder::class],
        ];
        // @codingStandardsIgnoreEnd
    }
}
