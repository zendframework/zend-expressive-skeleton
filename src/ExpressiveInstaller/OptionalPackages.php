<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ExpressiveInstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\Link;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Script\Event;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Composer installer script
 *
 * Add this script to composer.json:
 *
 *  "scripts": {
 *      "pre-update-cmd": "ExpressiveInstaller\\OptionalPackages::install",
 *      "pre-install-cmd": "ExpressiveInstaller\\OptionalPackages::install"
 *  },
 */
class OptionalPackages
{
    public const INSTALL_FLAT    = 'flat';
    public const INSTALL_MINIMAL = 'minimal';
    public const INSTALL_MODULAR = 'modular';

    /**
     * @const string Regular expression for matching package name and version
     */
    public const PACKAGE_REGEX = '/^(?P<name>[^:]+\/[^:]+)([:]*)(?P<version>.*)$/';

    /**
     * @const string Configuration file lines related to registering the default
     *     App module configuration.
     */
    public const APP_MODULE_CONFIG = '
    // Default App module config
    App\ConfigProvider::class,

';

    /**
     * Assets to remove during cleanup.
     *
     * @var string[]
     */
    private $assetsToRemove = [
        '.coveralls.yml',
        '.travis.yml',
        'CHANGELOG.md',
        'phpcs.xml',
        'phpstan.installer.neon',
        'src/App/templates/.gitkeep',
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var array
     */
    private $composerDefinition;

    /**
     * @var JsonFile
     */
    private $composerJson;

    /**
     * @var Link[]
     */
    private $composerRequires;

    /**
     * @var Link[]
     */
    private $composerDevRequires;

    /**
     * @var string[] Dev dependencies to remove after install is complete
     */
    private $devDependencies = [
        'composer/composer',
        'elie29/zend-phpdi-config',
        'filp/whoops',
        'jsoumelidis/zend-sf-di-config',
        'mikey179/vfsstream',
        'northwoods/container',
        "phpstan/phpstan",
        "phpstan/phpstan-strict-rules",
        'zendframework/zend-auradi-config',
        'zendframework/zend-coding-standard',
        'zendframework/zend-expressive-aurarouter',
        'zendframework/zend-expressive-fastroute',
        'zendframework/zend-expressive-platesrenderer',
        'zendframework/zend-expressive-twigrenderer',
        'zendframework/zend-expressive-zendrouter',
        'zendframework/zend-expressive-zendviewrenderer',
        'zendframework/zend-pimple-config',
        'zendframework/zend-servicemanager',
    ];

    /**
     * @var string Path to this file.
     */
    private $installerSource;

    /**
     * @var string Installation type selected.
     */
    private $installType = self::INSTALL_FLAT;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @var RootPackageInterface
     */
    private $rootPackage;

    /**
     * @var int[]
     */
    private $stabilityFlags;

    /**
     * Install command: choose packages and provide configuration.
     *
     * Prompts users for package selections, and copies in package-specific
     * configuration when known.
     *
     * Updates the composer.json with the package selections, and removes the
     * install and update commands on completion.
     *
     * @codeCoverageIgnore
     */
    public static function install(Event $event) : void
    {
        $installer = new self($event->getIO(), $event->getComposer());

        $installer->io->write('<info>Setting up optional packages</info>');

        $installer->setupDataAndCacheDir();
        $installer->removeDevDependencies();
        $installer->setInstallType($installer->requestInstallType());
        $installer->setupDefaultApp();
        $installer->promptForOptionalPackages();
        $installer->updateRootPackage();
        $installer->removeInstallerFromDefinition();
        $installer->finalizePackage();
    }

    public function __construct(IOInterface $io, Composer $composer, string $projectRoot = null)
    {
        $this->io = $io;
        $this->composer = $composer;

        // Get composer.json location
        $composerFile = Factory::getComposerFile();

        // Calculate project root from composer.json, if necessary
        $this->projectRoot = $projectRoot ?: realpath(dirname($composerFile));
        $this->projectRoot = rtrim($this->projectRoot, '/\\') . '/';

        // Parse the composer.json
        $this->parseComposerDefinition($composer, $composerFile);

        // Get optional packages configuration
        $this->config = require __DIR__ . '/config.php';

        // Source path for this file
        $this->installerSource = realpath(__DIR__) . '/';
    }

    /**
     * Create data and cache directories, if not present.
     *
     * Also sets up appropriate permissions.
     */
    public function setupDataAndCacheDir() : void
    {
        $this->io->write('<info>Setup data and cache dir</info>');
        if (! is_dir($this->projectRoot . '/data/cache')) {
            mkdir($this->projectRoot . '/data/cache', 0775, true);
            chmod($this->projectRoot . '/data', 0775);
        }
    }

    /**
     * Cleanup development dependencies.
     *
     * The dev dependencies should be removed from the stability flags,
     * require-dev and the composer file.
     */
    public function removeDevDependencies() : void
    {
        $this->io->write('<info>Removing installer development dependencies</info>');
        foreach ($this->devDependencies as $devDependency) {
            unset($this->stabilityFlags[$devDependency]);
            unset($this->composerDevRequires[$devDependency]);
            unset($this->composerDefinition['require-dev'][$devDependency]);
        }
    }

    /**
     * Prompt for the installation type.
     *
     * @return string One of the INSTALL_ constants.
     */
    public function requestInstallType() : string
    {
        $query = [
            sprintf(
                "\n  <question>%s</question>\n",
                'What type of installation would you like?'
            ),
            "  [<comment>1</comment>] Minimal (no default middleware, templates, or assets; configuration only)\n",
            "  [<comment>2</comment>] Flat (flat source code structure; default selection)\n",
            "  [<comment>3</comment>] Modular (modular source code structure; recommended)\n",
            '  Make your selection <comment>(2)</comment>: ',
        ];

        while (true) {
            $answer = $this->io->ask(implode($query), '2');

            switch (true) {
                case ($answer === '1'):
                    return self::INSTALL_MINIMAL;
                case ($answer === '2'):
                    return self::INSTALL_FLAT;
                case ($answer === '3'):
                    return self::INSTALL_MODULAR;
                default:
                    // @codeCoverageIgnoreStart
                    $this->io->write('<error>Invalid answer</error>');
                    // @codeCoverageIgnoreEnd
            }
        }
    }

    /**
     * Set the install type.
     */
    public function setInstallType(string $installType) : void
    {
        $this->installType =
            in_array($installType, [
                self::INSTALL_FLAT,
                self::INSTALL_MINIMAL,
                self::INSTALL_MODULAR,
            ], true)
            ? $installType
            : self::INSTALL_FLAT;
    }

    /**
     * Setup the default application structure.
     *
     * @throws RuntimeException if $installType is unknown
     */
    public function setupDefaultApp() : void
    {
        switch ($this->installType) {
            case self::INSTALL_MINIMAL:
                $this->removeDefaultModule();
                // no files to copy
                return;

            case self::INSTALL_FLAT:
                // Re-arrange files into a flat structure.
                $this->recursiveRmdir($this->projectRoot . '/src/App/templates');
                rename($this->projectRoot . '/src/App/src/Handler', $this->projectRoot . '/src/App/Handler');
                $this->recursiveRmdir($this->projectRoot . '/src/App/src');

                // Re-define autoloading rules
                $this->composerDefinition['autoload']['psr-4']['App\\'] = 'src/App/';
                break;

            case self::INSTALL_MODULAR:
                // Nothing additional to do
                break;

            default:
                throw new RuntimeException(sprintf(
                    'Invalid install type "%s"; this indicates a bug in the installer',
                    $this->installType
                ));
        }

        foreach ($this->config['application'][$this->installType]['packages'] as $package => $constraint) {
            $this->addPackage($package, $constraint);
        }

        foreach ($this->config['application'][$this->installType]['resources'] as $resource => $target) {
            $this->copyResource($resource, $target);
        }
    }

    /**
     * Prompt for each optional installation package.
     *
     * @codeCoverageIgnore
     */
    public function promptForOptionalPackages() : void
    {
        foreach ($this->config['questions'] as $questionName => $question) {
            $this->promptForOptionalPackage($questionName, $question);
        }
    }

    /**
     * Prompt for a single optional installation package.
     *
     * @param string $questionName Name of question
     * @param array $question Question details from configuration
     */
    public function promptForOptionalPackage(string $questionName, array $question) : void
    {
        $defaultOption = $question['default'] ?? 1;
        if (isset($this->composerDefinition['extra']['optional-packages'][$questionName])) {
            // Skip question, it's already answered
            return;
        }

        // Get answer
        $answer = $this->askQuestion($question, $defaultOption);

        // Process answer
        $this->processAnswer($question, $answer);

        // Save user selected option
        $this->composerDefinition['extra']['optional-packages'][$questionName] = $answer;

        // Update composer definition
        $this->composerJson->write($this->composerDefinition);
    }

    /**
     * Update the root package based on current state.
     */
    public function updateRootPackage() : void
    {
        $this->rootPackage->setRequires($this->composerRequires);
        $this->rootPackage->setDevRequires($this->composerDevRequires);
        $this->rootPackage->setStabilityFlags($this->stabilityFlags);
        $this->rootPackage->setAutoload($this->composerDefinition['autoload']);
        $this->rootPackage->setDevAutoload($this->composerDefinition['autoload-dev']);
        $this->rootPackage->setExtra($this->composerDefinition['extra'] ?? []);
    }

    /**
     * Remove the installer from the composer definition
     */
    public function removeInstallerFromDefinition() : void
    {
        $this->io->write('<info>Remove installer</info>');

        // Remove installer script autoloading rules
        unset($this->composerDefinition['autoload']['psr-4']['ExpressiveInstaller\\']);
        unset($this->composerDefinition['autoload-dev']['psr-4']['ExpressiveInstallerTest\\']);

        // Remove branch-alias
        unset($this->composerDefinition['extra']['branch-alias']);

        // Remove installer data
        unset($this->composerDefinition['extra']['optional-packages']);

        // Remove installer scripts
        unset($this->composerDefinition['scripts']['pre-update-cmd']);
        unset($this->composerDefinition['scripts']['pre-install-cmd']);
    }

    /**
     * Finalize the package.
     *
     * Writes the current JSON state to composer.json, clears the
     * composer.lock file, and cleans up all files specific to the
     * installer.
     *
     * @codeCoverageIgnore
     */
    public function finalizePackage() : void
    {
        // Update composer definition
        $this->composerJson->write($this->composerDefinition);

        $this->clearComposerLockFile();
        $this->cleanUp();
    }

    /**
     * Process the answer of a question
     *
     * @param bool|int|string $answer
     */
    public function processAnswer(array $question, $answer) : bool
    {
        if (is_numeric($answer) && isset($question['options'][$answer])) {
            // Add packages to install
            if (isset($question['options'][$answer]['packages'])) {
                foreach ($question['options'][$answer]['packages'] as $packageName) {
                    $packageData = $this->config['packages'][$packageName];
                    $this->addPackage($packageName, $packageData['version'], $packageData['whitelist'] ?? []);
                }
            }

            // Copy files
            if (isset($question['options'][$answer][$this->installType])) {
                $force = ! empty($question['force']);
                foreach ($question['options'][$answer][$this->installType] as $resource => $target) {
                    $this->copyResource($resource, $target, $force);
                }
            }

            return true;
        }

        if ($question['custom-package'] === true && preg_match(self::PACKAGE_REGEX, (string) $answer, $match)) {
            $this->addPackage($match['name'], $match['version'], []);
            if (isset($question['custom-package-warning'])) {
                $this->io->write(sprintf('  <warning>%s</warning>', $question['custom-package-warning']));
            }

            return true;
        }

        return false;
    }

    /**
     * Add a package
     */
    public function addPackage(string $packageName, string $packageVersion, array $whitelist = []) : void
    {
        $this->io->write(sprintf(
            '  - Adding package <info>%s</info> (<comment>%s</comment>)',
            $packageName,
            $packageVersion
        ));

        // Get the version constraint
        $versionParser = new VersionParser();
        $constraint    = $versionParser->parseConstraints($packageVersion);

        // Create package link
        $link = new Link('__root__', $packageName, $constraint, 'requires', $packageVersion);

        // Add package to the root package and composer.json requirements
        if (in_array($packageName, $this->config['require-dev'], true)) {
            unset($this->composerDefinition['require'][$packageName]);
            unset($this->composerRequires[$packageName]);

            $this->composerDefinition['require-dev'][$packageName] = $packageVersion;
            $this->composerDevRequires[$packageName]               = $link;
        } else {
            unset($this->composerDefinition['require-dev'][$packageName]);
            unset($this->composerDevRequires[$packageName]);

            $this->composerDefinition['require'][$packageName] = $packageVersion;
            $this->composerRequires[$packageName]              = $link;
        }

        // Set package stability if needed
        switch (VersionParser::parseStability($packageVersion)) {
            case 'dev':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_DEV;
                break;
            case 'alpha':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_ALPHA;
                break;
            case 'beta':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_BETA;
                break;
            case 'RC':
                $this->stabilityFlags[$packageName] = BasePackage::STABILITY_RC;
                break;
        }

        // Whitelist packages for the component installer
        foreach ($whitelist as $package) {
            if (! in_array($package, $this->composerDefinition['extra']['zf']['component-whitelist'], true)) {
                $this->composerDefinition['extra']['zf']['component-whitelist'][] = $package;
                $this->io->write(sprintf('  - Whitelist package <info>%s</info>', $package));
            }
        }
    }

    /**
     * Copy a file to its final destination in the skeleton.
     *
     * @param string $resource Resource file.
     * @param string $target Destination.
     * @param bool $force Whether or not to copy over an existing file.
     */
    public function copyResource(string $resource, string $target, bool $force = false) : void
    {
        // Copy file
        if ($force === false && is_file($this->projectRoot . $target)) {
            return;
        }

        $destinationPath = dirname($this->projectRoot . $target);
        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }

        $this->io->write(sprintf('  - Copying <info>%s</info>', $target));
        copy($this->installerSource . $resource, $this->projectRoot . $target);
    }

    /**
     * Remove lines from string content containing words in array.
     */
    public function removeLinesContainingStrings(array $entries, string $content) : string
    {
        $entries = implode('|', array_map(function ($word) {
            return preg_quote($word, '/');
        }, $entries));

        return preg_replace('/^.*(?:' . $entries . ").*$(?:\r?\n)?/m", '', $content);
    }

    /**
     * Clean up/remove installer classes and assets.
     *
     * On completion of install/update, removes the installer classes (including
     * this one) and assets (including configuration and templates).
     *
     * @codeCoverageIgnore
     */
    private function cleanUp() : void
    {
        $this->io->write('<info>Removing Expressive installer classes, configuration, tests and docs</info>');
        foreach ($this->assetsToRemove as $target) {
            $target = $this->projectRoot . $target;
            if (file_exists($target)) {
                unlink($target);
            }
        }

        $this->recursiveRmdir($this->installerSource);
        $this->recursiveRmdir($this->projectRoot . 'test/ExpressiveInstallerTest');
        $this->recursiveRmdir($this->projectRoot . 'docs');

        $this->preparePhpunitConfig();
    }

    /**
     * Remove the ExpressiveInstaller exclusion from the phpunit configuration
     *
     * @codeCoverageIgnore
     */
    private function preparePhpunitConfig() : void
    {
        $phpunitConfigFile = $this->projectRoot . 'phpunit.xml.dist';
        $phpunitConfig     = file_get_contents($phpunitConfigFile);
        $phpunitConfig     = $this->removeLinesContainingStrings(['exclude', 'ExpressiveInstaller'], $phpunitConfig);
        file_put_contents($phpunitConfigFile, $phpunitConfig);
    }

    /**
     * Prepare and ask questions and return the answer
     *
     * @param int|string $defaultOption
     * @return bool|int|string
     * @codeCoverageIgnore
     */
    private function askQuestion(array $question, $defaultOption)
    {
        // Construct question
        $ask = [
            sprintf("\n  <question>%s</question>\n", $question['question']),
        ];

        $defaultText = $defaultOption;

        foreach ($question['options'] as $key => $option) {
            $defaultText = ($key === $defaultOption) ? $option['name'] : $defaultText;
            $ask[]       = sprintf("  [<comment>%d</comment>] %s\n", $key, $option['name']);
        }

        if ($question['required'] !== true) {
            $ask[] = "  [<comment>n</comment>] None of the above\n";
        }

        $ask[] = ($question['custom-package'] === true)
            ? sprintf(
                '  Make your selection or type a composer package name and version <comment>(%s)</comment>: ',
                $defaultText
            )
            : sprintf('  Make your selection <comment>(%s)</comment>: ', $defaultText);

        while (true) {
            // Ask for user input
            $answer = $this->io->ask(implode($ask), (string) $defaultOption);

            // Handle none of the options
            if ($answer === 'n' && $question['required'] !== true) {
                return 'n';
            }

            // Handle numeric options
            if (is_numeric($answer) && isset($question['options'][(int) $answer])) {
                return (int) $answer;
            }

            // Search for package
            if ($question['custom-package'] === true && preg_match(self::PACKAGE_REGEX, $answer, $match)) {
                $packageName    = $match['name'];
                $packageVersion = $match['version'];

                if (! $packageVersion) {
                    $this->io->write('<error>No package version specified</error>');
                    continue;
                }

                $this->io->write(sprintf('  - Searching for <info>%s:%s</info>', $packageName, $packageVersion));

                $optionalPackage = $this->composer->getRepositoryManager()->findPackage($packageName, $packageVersion);
                if (null === $optionalPackage) {
                    $this->io->write(sprintf('<error>Package not found %s:%s</error>', $packageName, $packageVersion));
                    continue;
                }

                return sprintf('%s:%s', $packageName, $packageVersion);
            }

            $this->io->write('<error>Invalid answer</error>');
        }

        return false;
    }

    /**
     * If a minimal install was requested, remove the default middleware and assets.
     *
     * @codeCoverageIgnore
     */
    private function removeDefaultModule() : void
    {
        $this->io->write('<info>Removing default App module classes and factories</info>');
        $this->recursiveRmdir($this->projectRoot . '/src/App');

        $this->io->write('<info>Removing default App module tests</info>');
        $this->recursiveRmdir($this->projectRoot . '/test/AppTest');

        $this->io->write('<info>Removing App module registration from configuration</info>');
        $this->removeAppModuleConfig();
    }

    /**
     * Recursively remove a directory.
     *
     * @codeCoverageIgnore
     */
    private function recursiveRmdir(string $directory) : void
    {
        if (! is_dir($directory)) {
            return;
        }

        $rdi = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($rii as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
                continue;
            }
            unlink($filename);
        }
        rmdir($directory);
    }

    /**
     * Removes composer.lock file from gitignore.
     *
     * @codeCoverageIgnore
     */
    private function clearComposerLockFile() : void
    {
        $this->io->write('<info>Removing composer.lock from .gitignore</info>');

        $ignoreFile = sprintf('%s/.gitignore', $this->projectRoot);

        $content = $this->removeLinesContainingStrings(['composer.lock'], file_get_contents($ignoreFile));
        file_put_contents($ignoreFile, $content);
    }

    /**
     * Removes the App\ConfigProvider entry from the application config file.
     */
    private function removeAppModuleConfig() : void
    {
        $configFile = $this->projectRoot . '/config/config.php';
        $contents = file_get_contents($configFile);
        $contents = str_replace(self::APP_MODULE_CONFIG, '', $contents);
        file_put_contents($configFile, $contents);
    }

    /**
     * Parses the composer file and populates internal data
     */
    private function parseComposerDefinition(Composer $composer, string $composerFile) : void
    {
        $this->composerJson = new JsonFile($composerFile);
        $this->composerDefinition = $this->composerJson->read();

        // Get root package or root alias package
        $this->rootPackage = $composer->getPackage();

        // Get required packages
        $this->composerRequires    = $this->rootPackage->getRequires();
        $this->composerDevRequires = $this->rootPackage->getDevRequires();

        // Get stability flags
        $this->stabilityFlags = $this->rootPackage->getStabilityFlags();
    }
}
