<?php

return [
    'packages' => [
        'aura/di'                                        => '^3.0',
        'filp/whoops'                                    => '^1.1 || ^2.0',
        'xtreamwayz/pimple-container-interop'            => '^1.0',
        'zendframework/zend-expressive-aurarouter'       => '^1.0',
        'zendframework/zend-expressive-fastroute'        => '^1.0',
        'zendframework/zend-expressive-platesrenderer'   => '^1.0',
        'zendframework/zend-expressive-twigrenderer'     => '^1.0',
        'zendframework/zend-expressive-zendrouter'       => '^1.0',
        'zendframework/zend-expressive-zendviewrenderer' => '^1.0',
        'zendframework/zend-servicemanager'              => '^2.7.3 || ^3.0',
    ],

    'require-dev' => [
        'filp/whoops'
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
                    'copy-files' => [
                        '/Resources/config/container-aura-di.php' => '/config/container.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/container-aura-di.php' => '/config/container.php',
                    ],
                ],
                2 => [
                    'name'     => 'Pimple',
                    'packages' => [
                        'xtreamwayz/pimple-container-interop',
                    ],
                    'copy-files' => [
                        '/Resources/config/container-pimple.php' => '/config/container.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/container-pimple.php' => '/config/container.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend ServiceManager',
                    'packages' => [
                        'zendframework/zend-servicemanager',
                    ],
                    'copy-files' => [
                        '/Resources/config/container-zend-servicemanager.php' => '/config/container.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/container-zend-servicemanager.php' => '/config/container.php',
                    ],
                ],
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
                    'copy-files' => [
                        // Copy source file to target: '<source>' => '<target>'
                        '/Resources/config/routes-aura-router.php' => '/config/autoload/routes.global.php',
                    ],
                    'minimal-files' => [
                        // Copy source file to target: '<source>' => '<target>'
                        '/Resources/config/routes-minimal-aura-router.php' => '/config/autoload/routes.global.php',
                    ],
                ],
                2 => [
                    'name'     => 'FastRoute',
                    'packages' => [
                        'zendframework/zend-expressive-fastroute',
                    ],
                    'copy-files' => [
                        '/Resources/config/routes-fast-route.php' => '/config/autoload/routes.global.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/routes-minimal-fast-route.php' => '/config/autoload/routes.global.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend Router',
                    'packages' => [
                        'zendframework/zend-expressive-zendrouter',
                    ],
                    'copy-files' => [
                        '/Resources/config/routes-zf2-router.php' => '/config/autoload/routes.global.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/routes-minimal-zf2-router.php' => '/config/autoload/routes.global.php',
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
                    'copy-files' => [
                        '/Resources/config/templates-plates.php' => '/config/autoload/templates.global.php',
                        '/Resources/templates/plates-404.phtml' => '/templates/error/404.phtml',
                        '/Resources/templates/plates-error.phtml' => '/templates/error/error.phtml',
                        '/Resources/templates/plates-layout.phtml' => '/templates/layout/default.phtml',
                        '/Resources/templates/plates-home-page.phtml' => '/templates/app/home-page.phtml',
                    ],
                    'minimal-files' => [
                        '/Resources/config/templates-plates.php' => '/config/autoload/templates.global.php',
                    ],
                ],
                2 => [
                    'name'     => 'Twig',
                    'packages' => [
                        'zendframework/zend-expressive-twigrenderer',
                    ],
                    'copy-files' => [
                        '/Resources/config/templates-twig.php' => '/config/autoload/templates.global.php',
                        '/Resources/templates/twig-404.html.twig' => '/templates/error/404.html.twig',
                        '/Resources/templates/twig-error.html.twig' => '/templates/error/error.html.twig',
                        '/Resources/templates/twig-layout.html.twig' => '/templates/layout/default.html.twig',
                        '/Resources/templates/twig-home-page.html.twig' => '/templates/app/home-page.html.twig',
                    ],
                    'minimal-files' => [
                        '/Resources/config/templates-twig.php' => '/config/autoload/templates.global.php',
                    ],
                ],
                3 => [
                    'name'     => 'Zend View <comment>installs Zend ServiceManager</comment>',
                    'packages' => [
                        'zendframework/zend-expressive-zendviewrenderer',
                    ],
                    'copy-files' => [
                        '/Resources/config/templates-zend-view.php' => '/config/autoload/templates.global.php',
                        '/Resources/templates/zend-view-404.phtml' => '/templates/error/404.phtml',
                        '/Resources/templates/zend-view-error.phtml' => '/templates/error/error.phtml',
                        '/Resources/templates/zend-view-layout.phtml' => '/templates/layout/default.phtml',
                        '/Resources/templates/zend-view-home-page.phtml' => '/templates/app/home-page.phtml',
                    ],
                    'minimal-files' => [
                        '/Resources/config/templates-zend-view.php' => '/config/autoload/templates.global.php',
                    ],
                ],
            ],
        ],

        'error-handler' => [
            'question'       => 'Which error handler do you want to use during development?',
            'default'        => 1,
            'required'       => false,
            'custom-package' => true,
            'options'        => [
                1 => [
                    'name'     => 'Whoops',
                    'packages' => [
                        'filp/whoops',
                    ],
                    'copy-files' => [
                        '/Resources/config/error-handler-whoops.php' => '/config/autoload/errorhandler.local.php',
                    ],
                    'minimal-files' => [
                        '/Resources/config/error-handler-whoops.php' => '/config/autoload/errorhandler.local.php',
                    ],
                ],
            ],
        ],
    ],
];
