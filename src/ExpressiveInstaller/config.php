<?php

return [
    'packages' => [
        'aura/di'                                        => '^3.2',
        'filp/whoops'                                    => '^2.1.7',
        'xtreamwayz/pimple-container-interop'            => '^1.0',
        'zendframework/zend-expressive-aurarouter'       => '^2.0',
        'zendframework/zend-expressive-fastroute'        => '^2.0',
        'zendframework/zend-expressive-platesrenderer'   => '^1.3.1',
        'zendframework/zend-expressive-twigrenderer'     => '^1.4',
        'zendframework/zend-expressive-zendrouter'       => '^2.0.1',
        'zendframework/zend-expressive-zendviewrenderer' => '^1.4',
        'zendframework/zend-servicemanager'              => '^3.3',
        'jsoumelidis/zend-sm-sf-di-bridge'               => '^0.1',
        'jsoumelidis/symfony-di-container-interop'       => '^1.0',
    ],

    'require-dev' => [
        'filp/whoops',
        'zendframework/zend-expressive-tooling',
    ],

    'application' => [
        'flat' => [
            'packages' => [],
            'resources' => [
                'Resources/src/ConfigProvider.flat.php' => 'src/App/ConfigProvider.php',
            ],
        ],
        'modular' => [
            'packages' => [
                'zendframework/zend-expressive-tooling' => '^0.4.1',
            ],
            'resources' => [
                'Resources/src/ConfigProvider.modular.php' => 'src/App/src/ConfigProvider.php',
            ],
        ],
    ],

    'questions' => [
        'container' => [
            'question'               => 'Which container do you want to use for dependency injection?',
            'default'                => 3,
            'required'               => true,
            'custom-package'         => true,
            'custom-package-warning' => 'You need to edit public/index.php to start the custom container.',
            'options'                => [
                1 => [
                    'name'     => 'Aura.Di',
                    'packages' => [
                        'aura/di',
                    ],
                    // @codingStandardsIgnoreStart
                    'flat' => [
                        'Resources/config/container-aura-di.php'           => 'config/container.php',
                        'Resources/src/ExpressiveAuraConfig.php'           => 'config/ExpressiveAuraConfig.php',
                        'Resources/src/ExpressiveAuraDelegatorFactory.php' => 'config/ExpressiveAuraDelegatorFactory.php',
                    ],
                    'modular' => [
                        'Resources/config/container-aura-di.php'           => 'config/container.php',
                        'Resources/src/ExpressiveAuraConfig.php'           => 'config/ExpressiveAuraConfig.php',
                        'Resources/src/ExpressiveAuraDelegatorFactory.php' => 'config/ExpressiveAuraDelegatorFactory.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-aura-di.php'           => 'config/container.php',
                        'Resources/src/ExpressiveAuraConfig.php'           => 'config/ExpressiveAuraConfig.php',
                        'Resources/src/ExpressiveAuraDelegatorFactory.php' => 'config/ExpressiveAuraDelegatorFactory.php',
                    ],
                    // @codingStandardsIgnoreEnd
                ],
                2 => [
                    'name'     => 'Pimple',
                    'packages' => [
                        'xtreamwayz/pimple-container-interop',
                    ],
                    'flat' => [
                        'Resources/config/container-pimple.php' => 'config/container.php',
                    ],
                    'modular' => [
                        'Resources/config/container-pimple.php' => 'config/container.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-pimple.php' => 'config/container.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend ServiceManager',
                    'packages' => [
                        'zendframework/zend-servicemanager',
                    ],
                    'flat' => [
                        'Resources/config/container-zend-servicemanager.php' => 'config/container.php',
                    ],
                    'modular' => [
                        'Resources/config/container-zend-servicemanager.php' => 'config/container.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-zend-servicemanager.php' => 'config/container.php',
                    ],
                ],
                4 => [
                    'name'     => 'Symfony DI Container',
                    'packages' => [
                        'jsoumelidis/zend-sm-sf-di-bridge',
                        'jsoumelidis/symfony-di-container-interop',
                    ],
                    'flat' => [
                        'Resources/config/container-sf-di.php'     => 'config/container.php',
                        'Resources/config/sf-container.global.php' => 'config/autoload/sf-container.global.php',
                        'Resources/src/SfContainerConfig.php'      => 'config/SfContainerConfig.php',
                    ],
                    'modular' => [
                        'Resources/config/container-sf-di.php'     => 'config/container.php',
                        'Resources/config/sf-container.global.php' => 'config/autoload/sf-container.global.php',
                        'Resources/src/SfContainerConfig.php'      => 'config/SfContainerConfig.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-sf-di.php'     => 'config/container.php',
                        'Resources/config/sf-container.global.php' => 'config/autoload/sf-container.global.php',
                        'Resources/src/SfContainerConfig.php'      => 'config/SfContainerConfig.php',
                    ],
                ]
            ],
        ],

        'router' => [
            'question'               => 'Which router do you want to use?',
            'default'                => 2,
            // TRUE: Must choose one / FALSE: May choose one or none of the above
            'required'               => true,
            // Enable custom package input
            'custom-package'         => true,
            // Display warning when choosing a custom package
            'custom-package-warning' => 'You need to write your own router adapter.',
            'options'                => [
                1 => [
                    'name'     => 'Aura.Router',
                    'packages' => [
                        'zendframework/zend-expressive-aurarouter',
                    ],
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-aura-router.php' => 'config/autoload/router.global.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-aura-router.php' => 'config/autoload/router.global.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
                        'Resources/config/router-aura-router.php' => 'config/autoload/router.global.php',
                    ],
                ],
                2 => [
                    'name'     => 'FastRoute',
                    'packages' => [
                        'zendframework/zend-expressive-fastroute',
                    ],
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-fast-route.php' => 'config/autoload/router.global.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-fast-route.php' => 'config/autoload/router.global.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
                        'Resources/config/router-fast-route.php' => 'config/autoload/router.global.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend Router',
                    'packages' => [
                        'zendframework/zend-expressive-zendrouter',
                    ],
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-zend-router.php' => 'config/autoload/router.global.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                        'Resources/config/router-zend-router.php' => 'config/autoload/router.global.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
                        'Resources/config/router-zend-router.php' => 'config/autoload/router.global.php',
                    ],
                ],
            ],
        ],

        'template-engine' => [
            'question'       => 'Which template engine do you want to use?',
            'default'        => 'n',
            'required'       => false,
            'custom-package' => true,
            'options'        => [
                1 => [
                    'name'     => 'Plates',
                    'packages' => [
                        'zendframework/zend-expressive-platesrenderer',
                    ],
                    'flat' => [
                        'Resources/config/templates-plates.php'      => 'config/autoload/templates.global.php',
                        'Resources/templates/plates-404.phtml'       => 'templates/error/404.phtml',
                        'Resources/templates/plates-error.phtml'     => 'templates/error/error.phtml',
                        'Resources/templates/plates-layout.phtml'    => 'templates/layout/default.phtml',
                        'Resources/templates/plates-home-page.phtml' => 'templates/app/home-page.phtml',
                    ],
                    'modular' => [
                        'Resources/config/templates-plates.php'      => 'config/autoload/templates.global.php',
                        'Resources/templates/plates-404.phtml'       => 'src/App/templates/error/404.phtml',
                        'Resources/templates/plates-error.phtml'     => 'src/App/templates/error/error.phtml',
                        'Resources/templates/plates-layout.phtml'    => 'src/App/templates/layout/default.phtml',
                        'Resources/templates/plates-home-page.phtml' => 'src/App/templates/app/home-page.phtml',
                    ],
                    'minimal' => [
                        'Resources/config/templates-plates.php' => 'config/autoload/templates.global.php',
                    ],
                ],
                2 => [
                    'name'     => 'Twig',
                    'packages' => [
                        'zendframework/zend-expressive-twigrenderer',
                    ],
                    'flat' => [
                        'Resources/config/templates-twig.php'          => 'config/autoload/templates.global.php',
                        'Resources/templates/twig-404.html.twig'       => 'templates/error/404.html.twig',
                        'Resources/templates/twig-error.html.twig'     => 'templates/error/error.html.twig',
                        'Resources/templates/twig-layout.html.twig'    => 'templates/layout/default.html.twig',
                        'Resources/templates/twig-home-page.html.twig' => 'templates/app/home-page.html.twig',
                    ],
                    'modular' => [
                        'Resources/config/templates-twig.php'          => 'config/autoload/templates.global.php',
                        'Resources/templates/twig-404.html.twig'       => 'src/App/templates/error/404.html.twig',
                        'Resources/templates/twig-error.html.twig'     => 'src/App/templates/error/error.html.twig',
                        'Resources/templates/twig-layout.html.twig'    => 'src/App/templates/layout/default.html.twig',
                        'Resources/templates/twig-home-page.html.twig' => 'src/App/templates/app/home-page.html.twig',
                    ],
                    'minimal' => [
                        'Resources/config/templates-twig.php' => 'config/autoload/templates.global.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend View <comment>installs Zend ServiceManager</comment>',
                    'packages' => [
                        'zendframework/zend-expressive-zendviewrenderer',
                    ],
                    'flat' => [
                        'Resources/config/templates-zend-view.php'      => 'config/autoload/templates.global.php',
                        'Resources/templates/zend-view-404.phtml'       => 'templates/error/404.phtml',
                        'Resources/templates/zend-view-error.phtml'     => 'templates/error/error.phtml',
                        'Resources/templates/zend-view-layout.phtml'    => 'templates/layout/default.phtml',
                        'Resources/templates/zend-view-home-page.phtml' => 'templates/app/home-page.phtml',
                    ],
                    'modular' => [
                        'Resources/config/templates-zend-view.php'      => 'config/autoload/templates.global.php',
                        'Resources/templates/zend-view-404.phtml'       => 'src/App/templates/error/404.phtml',
                        'Resources/templates/zend-view-error.phtml'     => 'src/App/templates/error/error.phtml',
                        'Resources/templates/zend-view-layout.phtml'    => 'src/App/templates/layout/default.phtml',
                        'Resources/templates/zend-view-home-page.phtml' => 'src/App/templates/app/home-page.phtml',
                    ],
                    'minimal' => [
                        'Resources/config/templates-zend-view.php' => 'config/autoload/templates.global.php',
                    ],
                ],
            ],
        ],

        'error-handler' => [
            'question'       => 'Which error handler do you want to use during development?',
            'default'        => 1,
            'required'       => false,
            'custom-package' => true,
            'force'          => true,
            'options'        => [
                1 => [
                    'name'     => 'Whoops',
                    'packages' => [
                        'filp/whoops',
                    ],
                    'flat' => [
                        'Resources/config/error-handler-whoops.php' => 'config/autoload/development.local.php.dist',
                    ],
                    'modular' => [
                        'Resources/config/error-handler-whoops.php' => 'config/autoload/development.local.php.dist',
                    ],
                    'minimal' => [
                        'Resources/config/error-handler-whoops.php' => 'config/autoload/development.local.php.dist',
                    ],
                ],
            ],
        ],
    ],
];
