<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ExpressiveInstallerTest;

use Generator;
use Zend\Expressive;
use Zend\Expressive\Router\FastRouteRouter\ConfigProvider as FastRouteRouterConfigProvider;
use ExpressiveInstaller\OptionalPackages;
// Containers imports
use Psr\Container\ContainerInterface;
use Aura\Di\Container as AuraContainer;
use Northwoods\Container\InjectorContainer as AurynContainer;
use Pimple\Psr11\Container as PimpleContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;
use Zend\ServiceManager\ServiceManager as ZendServiceManagerContainer;
use DI\Container as PhpDIContainer;
// Renderers imports
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Plates\ConfigProvider as PlatesRendererConfigProvider;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\Twig\ConfigProvider as TwigRendererConfigProvider;
use Zend\Expressive\ZendView\ZendViewRenderer;
use Zend\Expressive\ZendView\ConfigProvider as ZendViewRendererConfigProvider;

class HomePageResponseTest extends OptionalPackagesTestCase
{
    use ProjectSandboxTrait;

    /**
     * @var OptionalPackages
     */
    private $installer;

    private $rendererConfigProviders = [
        PlatesRenderer::class   => PlatesRendererConfigProvider::class,
        TwigRenderer::class     => TwigRendererConfigProvider::class,
        ZendViewRenderer::class => ZendViewRendererConfigProvider::class,
    ];

    // $intallType, $intallType
    private $intallTypes = [
        OptionalPackages::INSTALL_FLAT    => OptionalPackages::INSTALL_FLAT,
        OptionalPackages::INSTALL_MODULAR => OptionalPackages::INSTALL_MODULAR,
    ];

    // $rendererOption, $rendererClass
    private $rendererTypes = [
        'plates'    => [1, PlatesRenderer::class],
        'twig'      => [2, TwigRenderer::class],
        'zend-view' => [3, ZendViewRenderer::class],
    ];

    // $containerOption, $containerClass
    private $containerTypes = [
        'aura'    => [1, AuraContainer::class],
        'pimple'  => [2, PimpleContainer::class],
        'zend-sm' => [3, ZendServiceManagerContainer::class],
        'auryn'   => [4, AurynContainer::class],
        'sf-di'   => [5, SfContainerBuilder::class],
        'php-di'  => [6, PhpDIContainer::class],
    ];

    private $expectedContainerAttributes = [
        AuraContainer::class => [
            'containerName' => 'Aura.Di',
            'containerDocs' => 'http://auraphp.com/packages/2.x/Di.html',
        ],
        PimpleContainer::class => [
            'containerName' => 'Pimple',
            'containerDocs' => 'https://pimple.symfony.com/',
        ],
        ZendServiceManagerContainer::class => [
            'containerName' => 'Zend Servicemanager',
            'containerDocs' => 'https://docs.zendframework.com/zend-servicemanager/',
        ],
        AurynContainer::class => [
            'containerName' => 'Auryn',
            'containerDocs' => 'https://github.com/rdlowrey/Auryn',
        ],
        SfContainerBuilder::class => [
            'containerName' => 'Symfony DI Container',
            'containerDocs' => 'https://symfony.com/doc/current/service_container.html',
        ],
        PhpDIContainer::class => [
            'containerName' => 'PHP-DI',
            'containerDocs' => 'http://php-di.org',
        ],
    ];

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
     * @dataProvider installCasesProvider
     */
    public function testHomePageResponseContainsCorrectCountainerInfo(
        string $installType,
        int $containerOption,
        string $containerClass,
        int $rendererOption,
        string $rendererClass,
        string $containerName,
        string $containerDocs
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
            $routerOption = 2 // FastRoute, use assignment for clarity
        );
        $this->assertTrue($routerResult);
        $this->injectRouterConfigProvider();

        // Install template engine
        $templateEngineResult = $this->installer->processAnswer(
            $config['questions']['template-engine'],
            $rendererOption
        );
        $this->assertTrue($templateEngineResult);
        $this->injectRendererConfigProvider($rendererClass);

        // Test home page response
        $response = $this->getAppResponse('/', true);
        $this->assertEquals(200, $response->getStatusCode());

        // Test response content
        $html = (string) $response->getBody()->getContents();

        $this->assertStringContainsString("Get started with {$containerName}", $html);
        $this->assertStringContainsString("href=\"{$containerDocs}\"", $html);
    }

    public function installCasesProvider() : Generator
    {
        // Execute a test case for each container, renderer and non minimal install type
        foreach ($this->containerTypes as $containerID => $containerType) {
            $containerOption = $containerType[0];
            $containerClass  = $containerType[1];

            $containerName = $this->expectedContainerAttributes[$containerClass]['containerName'];
            $containerDocs = $this->expectedContainerAttributes[$containerClass]['containerDocs'];

            foreach ($this->rendererTypes as $rendererID => $rendererType) {
                $rendererOption = $rendererType[0];
                $rendererClass  = $rendererType[1];

                foreach ($this->intallTypes as $intallType) {
                    $name = implode('-', [$containerID, $rendererID, $intallType]);
                    $args = [
                        $intallType,
                        $containerOption,
                        $containerClass,
                        $rendererOption,
                        $rendererClass,
                        $containerName,
                        $containerDocs,
                    ];

                    yield $name => $args;
                }
            }
        }
    }

    public function injectRouterConfigProvider()
    {
        $configFile = $this->projectRoot . '/config/config.php';
        $contents = file_get_contents($configFile);
        $contents = preg_replace(
            '/(new ConfigAggregator\(\[)/s',
            '$1' . "\n    " . FastRouteRouterConfigProvider::class . "::class,\n",
            $contents
        );
        file_put_contents($configFile, $contents);
    }

    public function injectRendererConfigProvider(string $rendererClass)
    {
        $configFile = $this->projectRoot . '/config/config.php';
        $contents = file_get_contents($configFile);
        $contents = preg_replace(
            '/(new ConfigAggregator\(\[)/s',
            '$1' . "\n    " . $this->rendererConfigProviders[$rendererClass] . "::class,\n",
            $contents
        );
        file_put_contents($configFile, $contents);
    }
}
