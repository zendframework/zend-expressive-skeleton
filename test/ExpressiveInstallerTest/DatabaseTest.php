<?php

/**
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

namespace ExpressiveInstallerTest;

use Doctrine\ORM\EntityManager;
use ExpressiveInstaller\OptionalPackages;
use Kocal\Expressive\Database as ExpressiveDatabase;
use Zend\Expressive;
use Zend\Stratigility\Middleware;

class DatabaseTest extends OptionalPackagesTestCase
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
        $this->installer = $this->createOptionalPackages($this->projectRoot);
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
     * @dataProvider databaseProvider
     *
     * @param string $installType
     * @param int $containerOption
     * @param int $routerOption
     * @param int $databaseOption
     * @param string $expectedDatabase
     */
    public function testDatabase(
        $installType,
        $containerOption,
        $routerOption,
        $databaseOption,
        $expectedDatabase
    )
    {
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

        // Install database ORM
        $databaseResult = $this->installer->processAnswer(
            $config['questions']['database'],
            $databaseOption
        );
        $this->assertTrue($databaseResult);

        // Test container
        $container = $this->getContainer();
        $this->assertTrue($container->has(Expressive\Application::class));
        $this->assertTrue($container->has(Middleware\ErrorHandler::class));

        // Test config
        $config = $container->get('config');

        switch ($databaseOption) {
            case 1:
                $this->assertEquals(ExpressiveDatabase\Doctrine\EntityManagerFactory::class, $config['dependencies']['factories'][EntityManager::class]);
                $this->assertContains('doctrine', $config);
                $this->assertContains('entities_path', $config);
                break;
            default:
                throw new \RuntimeException("Unrecognized database ORM.");
        }

        // Test database
        switch ($databaseOption) {
            case 1:
                $entityManager = $container->get(EntityManager::class);
                $this->assertInstanceOf($expectedDatabase, $entityManager);
                break;
            default:
                throw new \RuntimeException("Unrecognized database ORM.");
        }

        if ($installType !== OptionalPackages::INSTALL_MINIMAL) {
            $this->assertFileExists($this->projectRoot . '/src/App/Entity/Post.php');
            $this->assertFileExists($this->projectRoot . '/src/App/Repository/PostRepository.php');
        }
    }

    public function databaseProvider()
    {
        // @codingStandardsIgnoreStart
        // Minimal framework installation test cases; no entities/repositories installed.
        // $installType, $containerOption, $routerOption, $databaseOption, $expectedDatabase
        yield 'doctrine-minimal' => [OptionalPackages::INSTALL_MINIMAL, 3, 2, 1, EntityManager::class];

        // @codingStandardsIgnoreStart
        // Full framework installation test cases; installation options that install entities/repositories.
        $testCases = [
            // $containerOption, $routerOption, $databaseOption, $expectedDatabase
            'doctrine-full' => [3, 2, 1, EntityManager::class],
        ];
        // @codingStandardsIgnoreEnd

        // Non-minimal installation types
        $types = [
            OptionalPackages::INSTALL_FLAT,
            OptionalPackages::INSTALL_MODULAR,
        ];

        // Execute a test case for each install type
        foreach ($types as $type) {
            foreach ($testCases as $testName => $arguments) {
                array_unshift($arguments, $type);
                $name = sprintf('%s-%s', $type, $testName);
                yield $name => $arguments;
            }
        }
    }
}