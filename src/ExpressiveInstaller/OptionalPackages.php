<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @see       https://github.com/zendframework/zend-expressive-skeleton for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-skeleton/blob/master/LICENSE.md New BSD License
 */

namespace ExpressiveInstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\AliasPackage;
use Composer\Package\BasePackage;
use Composer\Package\Link;
use Composer\Package\Version\VersionParser;
use Composer\Script\Event;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
    /**
     * @const string Regular expression for matching package name and version
     */
    const PACKAGE_REGEX = '/^(?P<name>[^:]+\/[^:]+)([:]*)(?P<version>.*)$/';

    /**
     * @var array
     */
    private static $config;

    /**
     * @var string
     */
    private static $projectRoot;

    /**
     * @var array
     */
    private static $composerDefinition;

    /**
     * @var string[]
     */
    private static $composerRequires;

    /**
     * @var string[]
     */
    private static $composerDevRequires;

    /**
     * @var string[]
     */
    private static $stabilityFlags;

    private static $devDependencies = [
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

    /**
     * Install command: choose packages and provide configuration.
     *
     * Prompts users for package selections, and copies in package-specific
     * configuration when known.
     *
     * Updates the composer.json with the package selections, and removes the
     * install and update commands on completion.
     *
     * @param Event $event
     *
     * @codeCoverageIgnore
     */
    public static function install(Event $event)
    {
        $io       = $event->getIO();
        $composer = $event->getComposer();

        // Get composer.json
        $composerFile             = Factory::getComposerFile();
        $json                     = new JsonFile($composerFile);
        self::$composerDefinition = $json->read();

        self::$projectRoot = realpath(dirname($composerFile));

        $io->write("<info>Setup data and cache dir</info>");
        if (! is_dir(self::$projectRoot . '/data/cache')) {
            mkdir(self::$projectRoot . '/data/cache', 0775, true);
            chmod(self::$projectRoot . '/data', 0775);
        }

        $io->write("<info>Setting up optional packages</info>");

        // Get root package
        $rootPackage = $composer->getPackage();
        while ($rootPackage instanceof AliasPackage) {
            $rootPackage = $rootPackage->getAliasOf();
        }

        // Get required packages
        self::$composerRequires    = $rootPackage->getRequires();
        self::$composerDevRequires = $rootPackage->getDevRequires();

        // Get stability flags
        self::$stabilityFlags = $rootPackage->getStabilityFlags();

        // Cleanup development dependencies
        self::removeDevDependencies();

        // Minimal?
        $minimal      = self::requestMinimal($io);
        $copyFilesKey = $minimal ? 'minimal-files' : 'copy-files';

        self::$config = require __DIR__ . '/config.php';

        foreach (self::$config['questions'] as $questionName => $question) {
            $defaultOption = (isset($question['default'])) ? $question['default'] : 1;
            if (isset(self::$composerDefinition['extra']['optional-packages'][$questionName])) {
                // Skip question, it's already answered
                continue;
            }

            // Get answer
            $answer = self::askQuestion($composer, $io, $question, $defaultOption);

            // Process answer
            self::processAnswer($io, $question, $answer, $copyFilesKey);

            // Save user selected option
            self::$composerDefinition['extra']['optional-packages'][$questionName] = $answer;

            // Update composer definition
            $json->write(self::$composerDefinition);
        }

        // Set required packages
        $rootPackage->setRequires(self::$composerRequires);
        $rootPackage->setDevRequires(self::$composerDevRequires);

        // Set stability flags
        $rootPackage->setStabilityFlags(self::$stabilityFlags);

        // House keeping
        $io->write("<info>Remove installer</info>");
        self::removeInstallerFromDefinition();

        // Update composer definition
        $json->write(self::$composerDefinition);

        // Minimal install? Remove default middleware
        if ($minimal) {
            self::removeDefaultMiddleware($io, self::$projectRoot);
        }

        self::clearComposerLockFile($io, self::$projectRoot);
        self::cleanUp($io, self::$projectRoot);
    }

    /**
     * Cleanup development dependencies.
     *
     * The dev dependencies should be removed from the stability flags,
     * require-dev and the composer file.
     */
    public static function removeDevDependencies()
    {
        foreach (self::$devDependencies as $devDependency) {
            unset(self::$stabilityFlags[$devDependency]);
            unset(self::$composerDevRequires[$devDependency]);
            unset(self::$composerDefinition['require-dev'][$devDependency]);
        }
    }

    /**
     * Remove the installer from the composer definition
     */
    public static function removeInstallerFromDefinition()
    {
        // Remove installer script autoloading rules
        unset(self::$composerDefinition['autoload']['psr-4']['ExpressiveInstaller\\']);
        unset(self::$composerDefinition['autoload-dev']['psr-4']['ExpressiveInstallerTest\\']);

        // Remove branch-alias
        unset(self::$composerDefinition['extra']['branch-alias']);

        // Remove installer data
        unset(self::$composerDefinition['extra']['optional-packages']);

        // Remove left over
        if (empty(self::$composerDefinition['extra'])) {
            unset(self::$composerDefinition['extra']);
        }

        // Remove installer scripts
        unset(self::$composerDefinition['scripts']['pre-update-cmd']);
        unset(self::$composerDefinition['scripts']['pre-install-cmd']);
    }

    /**
     * Clean up/remove installer classes and assets.
     *
     * On completion of install/update, removes the installer classes (including
     * this one) and assets (including configuration and templates).
     *
     * @param IOInterface $io
     * @param string      $projectRoot
     *
     * @codeCoverageIgnore
     */
    private static function cleanUp(IOInterface $io, $projectRoot)
    {
        $io->write("<info>Removing Expressive installer classes, configuration, tests and docs</info>");
        unlink($projectRoot . '/.coveralls.yml');
        unlink($projectRoot . '/.travis.yml');
        unlink($projectRoot . '/CHANGELOG.md');
        unlink($projectRoot . '/CONDUCT.md');
        unlink($projectRoot . '/CONTRIBUTING.md');
        unlink($projectRoot . '/phpcs.xml');
        self::recursiveRmdir(__DIR__);
        self::recursiveRmdir($projectRoot . '/test/ExpressiveInstallerTest');

        // Remove ExpressiveInstaller exclusion from phpunit config
        $phpunitConfigFile = $projectRoot . '/phpunit.xml.dist';
        $phpunitConfig     = file_get_contents($phpunitConfigFile);
        $phpunitConfig     = self::removeLinesContainingStrings(['exclude', 'ExpressiveInstaller'], $phpunitConfig);
        file_put_contents($phpunitConfigFile, $phpunitConfig);
    }

    /**
     * Prepare and ask questions and return the answer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     * @param string      $question
     * @param string      $defaultOption
     *
     * @return bool|int|string
     * @codeCoverageIgnore
     */
    private static function askQuestion(Composer $composer, IOInterface $io, $question, $defaultOption)
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
                "  Make your selection or type a composer package name and version <comment>(%s)</comment>: ",
                $defaultText
            )
            : sprintf("  Make your selection <comment>(%s)</comment>: ", $defaultText);

        while (true) {
            // Ask for user input
            $answer = $io->ask($ask, $defaultOption);

            // Handle none of the options
            if ($answer == 'n' && $question['required'] !== true) {
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
                    $io->write("<error>No package version specified</error>");
                    continue;
                }

                $io->write(sprintf("  - Searching for <info>%s:%s</info>", $packageName, $packageVersion));

                $optionalPackage = $composer->getRepositoryManager()->findPackage($packageName, $packageVersion);
                if (! $optionalPackage) {
                    $io->write(sprintf("<error>Package not found %s:%s</error>", $packageName, $packageVersion));
                    continue;
                }

                return sprintf('%s:%s', $packageName, $packageVersion);
            }

            $io->write("<error>Invalid answer</error>");
        }

        return false;
    }

    /**
     * Process the answer of a question
     *
     * This process
     *
     * @param IOInterface $io
     * @param array       $question
     * @param string|int  $answer
     * @param string      $copyFilesKey
     *
     * @return bool
     */
    public static function processAnswer(IOInterface $io, array $question, $answer, $copyFilesKey)
    {
        if (is_numeric($answer) && isset($question['options'][$answer])) {
            // Add packages to install
            if (isset($question['options'][$answer]['packages'])) {
                foreach ($question['options'][$answer]['packages'] as $packageName) {
                    self::addPackage($io, $packageName, self::$config['packages'][$packageName]);
                }
            }

            // Copy files
            if (isset($question['options'][$answer][$copyFilesKey])) {
                foreach ($question['options'][$answer][$copyFilesKey] as $source => $target) {
                    self::copyFile($io, self::$projectRoot, $source, $target);
                }
            }

            return true;
        }

        if ($question['custom-package'] === true && preg_match(self::PACKAGE_REGEX, $answer, $match)) {
            self::addPackage($io, $match['name'], $match['version']);
            if (isset($question['custom-package-warning'])) {
                $io->write(sprintf("  <warning>%s</warning>", $question['custom-package-warning']));
            }

            return true;
        }

        return false;
    }

    /**
     * Add a package
     *
     * @param IOInterface $io
     * @param string      $packageName
     * @param string      $packageVersion
     */
    public static function addPackage(IOInterface $io, $packageName, $packageVersion)
    {
        $io->write(sprintf(
            "  - Adding package <info>%s</info> (<comment>%s</comment>)",
            $packageName,
            $packageVersion
        ));

        // Get the version constraint
        $versionParser = new VersionParser();
        $constraint    = $versionParser->parseConstraints($packageVersion);

        // Create package link
        $link = new Link('__root__', $packageName, $constraint, 'requires', $packageVersion);

        // Add package to the root package and composer.json requirements
        if (in_array($packageName, self::$config['require-dev'])) {
            unset(self::$composerDefinition['require'][$packageName]);
            unset(self::$composerRequires[$packageName]);

            self::$composerDefinition['require-dev'][$packageName] = $packageVersion;
            self::$composerDevRequires[$packageName]               = $link;
        } else {
            unset(self::$composerDefinition['require-dev'][$packageName]);
            unset(self::$composerDevRequires[$packageName]);

            self::$composerDefinition['require'][$packageName] = $packageVersion;
            self::$composerRequires[$packageName]              = $link;
        }

        // Set package stability if needed
        switch (VersionParser::parseStability($packageVersion)) {
            case 'dev':
                self::$stabilityFlags[$packageName] = BasePackage::STABILITY_DEV;
                break;
            case 'alpha':
                self::$stabilityFlags[$packageName] = BasePackage::STABILITY_ALPHA;
                break;
            case 'beta':
                self::$stabilityFlags[$packageName] = BasePackage::STABILITY_BETA;
                break;
            case 'RC':
                self::$stabilityFlags[$packageName] = BasePackage::STABILITY_RC;
                break;
        }
    }

    /**
     * Copy a file to its final destination in the skeleton.
     *
     * @param IOInterface $io
     * @param string      $projectRoot
     * @param string      $source Source file.
     * @param string      $target Destination.
     * @param bool        $force  whether or not to copy over an existing file.
     */
    public static function copyFile(IOInterface $io, $projectRoot, $source, $target, $force = false)
    {
        // Copy file
        if ($force === false && is_file($projectRoot . $target)) {
            return;
        }

        $destinationPath = dirname($projectRoot . $target);
        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }

        $io->write(sprintf("  - Copying <info>%s</info>", $target));
        copy(__DIR__ . $source, $projectRoot . $target);
    }

    /**
     * Remove lines from string content containing words in array.
     *
     * @param array  $entries Entries to remove.
     * @param string $content String to remove entry from.
     *
     * @return string
     */
    public static function removeLinesContainingStrings(array $entries, $content)
    {
        $entries = join('|', array_map(function ($word) {
            return preg_quote($word, '/');
        }, $entries));

        return preg_replace("/^.*(?:" . $entries . ").*$(?:\r?\n)?/m", '', $content);
    }

    /**
     * Ask if the user would like a minimal install.
     *
     * @param IOInterface $io
     *
     * @return bool
     */
    public static function requestMinimal(IOInterface $io)
    {
        $query = [
            sprintf(
                "\n  <question>%s</question>\n",
                'Minimal skeleton? (no default middleware, templates, or assets; configuration only)'
            ),
            "  [<comment>y</comment>] Yes (minimal)\n",
            "  [<comment>n</comment>] No (full; recommended)\n",
            "  Make your selection <comment>(No)</comment>: ",
        ];

        while (true) {
            $answer = $io->ask($query, 'n');

            if (strtolower($answer) === 'n') {
                return false;
            }

            if (strtolower($answer) === 'y') {
                return true;
            }

            // @codeCoverageIgnoreStart
            $io->write("<error>Invalid answer</error>");
            // @codeCoverageIgnoreEnd
        }

        // This should never be reached, defaults to default answer
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * If a minimal install was requested, remove the default middleware and assets.
     *
     * @param IOInterface $io
     * @param string      $projectRoot Project root from which to derive the directory to remove
     *
     * @codeCoverageIgnore
     */
    private static function removeDefaultMiddleware(IOInterface $io, $projectRoot)
    {
        $io->write("<info>Removing default middleware classes and factories</info>");
        self::recursiveRmdir($projectRoot . '/src/App/Action');

        $io->write("<info>Removing default middleware class tests</info>");
        self::recursiveRmdir($projectRoot . '/test/AppTest/Action');

        $io->write("<info>Removing assets</info>");
        unlink($projectRoot . '/public/favicon.ico');
        unlink($projectRoot . '/public/zf-logo.png');
    }

    /**
     * Recursively remove a directory.
     *
     * @param string $directory
     *
     * @codeCoverageIgnore
     */
    private static function recursiveRmdir($directory)
    {
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
     * @param IOInterface $io
     * @param string      $projectRoot
     *
     * @codeCoverageIgnore
     */
    private static function clearComposerLockFile($io, $projectRoot)
    {
        $io->write("<info>Removing composer.lock from .gitignore</info>");

        $ignoreFile = "$projectRoot/.gitignore";

        $content = self::removeLinesContainingStrings(['composer.lock'], file_get_contents($ignoreFile));
        file_put_contents($ignoreFile, $content);
    }
}
