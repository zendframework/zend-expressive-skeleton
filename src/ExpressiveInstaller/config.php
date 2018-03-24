<?php

declare(strict_types=1);

return [
    'packages' => [
        'filp/whoops' => [
            'version' => '^2.1.12',
        ],
        'jsoumelidis/zend-sf-di-config' => [
            'version' => '^0.2',
        ],
        'northwoods/container' => [
            'version' => '^1.2',
        ],
        'zendframework/zend-auradi-config' => [
            'version' => '^1.0',
        ],
        'zendframework/zend-expressive-aurarouter' => [
            'version'   => '^3.0',
            'whitelist' => [
                'zendframework/zend-expressive-aurarouter'
            ]
        ],
        'zendframework/zend-expressive-fastroute' => [
            'version'   => '^3.0',
            'whitelist' => [
                'zendframework/zend-expressive-fastroute'
            ]
        ],
        'zendframework/zend-expressive-platesrenderer' => [
            'version'   => '^2.0',
            'whitelist' => [
                'zendframework/zend-expressive-platesrenderer'
            ]
        ],
        'zendframework/zend-expressive-twigrenderer' => [
            'version'   => '^2.0',
            'whitelist' => [
                'zendframework/zend-expressive-twigrenderer'
            ]
        ],
        'zendframework/zend-expressive-zendrouter' => [
            'version'   => '^3.0',
            'whitelist' => [
                'zendframework/zend-expressive-zendrouter'
            ]
        ],
        'zendframework/zend-expressive-zendviewrenderer' => [
            'version'   => '^2.0',
            'whitelist' => [
                'zendframework/zend-expressive-zendviewrenderer'
            ]
        ],
        'zendframework/zend-pimple-config' => [
            'version' => '^1.0',
        ],
        'zendframework/zend-servicemanager' => [
            'version' => '^3.3',
        ],
    ],

    'require-dev' => [
        'filp/whoops',
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
                        'zendframework/zend-auradi-config',
                    ],
                    'flat' => [
                        'Resources/config/container-aura-di.php' => 'config/container.php',
                    ],
                    'modular' => [
                        'Resources/config/container-aura-di.php' => 'config/container.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-aura-di.php' => 'config/container.php',
                    ],
                ],
                2 => [
                    'name'     => 'Pimple',
                    'packages' => [
                        'zendframework/zend-pimple-config',
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
                    'name'     => 'zend-servicemanager',
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
                    'name'     => 'Auryn',
                    'packages' => [
                        'northwoods/container',
                    ],
                    'flat' => [
                        'Resources/config/container-auryn.php' => 'config/container.php',
                    ],
                    'modular' => [
                        'Resources/config/container-auryn.php' => 'config/container.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-auryn.php' => 'config/container.php',
                    ],
                ],
                5 => [
                    'name'     => 'Symfony DI Container',
                    'packages' => [
                        'jsoumelidis/zend-sf-di-config',
                    ],
                    'flat' => [
                        'Resources/config/container-sf-di.php' => 'config/container.php',
                    ],
                    'modular' => [
                        'Resources/config/container-sf-di.php' => 'config/container.php',
                    ],
                    'minimal' => [
                        'Resources/config/container-sf-di.php' => 'config/container.php',
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
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
                    ],
                ],
                2 => [
                    'name'     => 'FastRoute',
                    'packages' => [
                        'zendframework/zend-expressive-fastroute',
                    ],
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
                    ],
                ],
                3 => [
                    'name'     => 'zend-router',
                    'packages' => [
                        'zendframework/zend-expressive-zendrouter',
                    ],
                    'flat' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'modular' => [
                        'Resources/config/routes-full.php' => 'config/routes.php',
                    ],
                    'minimal' => [
                        'Resources/config/routes-minimal.php' => 'config/routes.php',
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
                        'Resources/templates/plates/404.phtml'       => 'templates/error/404.phtml',
                        'Resources/templates/plates/error.phtml'     => 'templates/error/error.phtml',
                        'Resources/templates/plates/layout.phtml'    => 'templates/layout/default.phtml',
                        'Resources/templates/plates/home-page.phtml' => 'templates/app/home-page.phtml',
                    ],
                    'modular' => [
                        'Resources/templates/plates/404.phtml'       => 'src/App/templates/error/404.phtml',
                        'Resources/templates/plates/error.phtml'     => 'src/App/templates/error/error.phtml',
                        'Resources/templates/plates/layout.phtml'    => 'src/App/templates/layout/default.phtml',
                        'Resources/templates/plates/home-page.phtml' => 'src/App/templates/app/home-page.phtml',
                    ],
                    'minimal' => [
                    ],
                ],
                2 => [
                    'name'     => 'Twig',
                    'packages' => [
                        'zendframework/zend-expressive-twigrenderer',
                    ],
                    'flat' => [
                        'Resources/templates/twig/404.html.twig'       => 'templates/error/404.html.twig',
                        'Resources/templates/twig/error.html.twig'     => 'templates/error/error.html.twig',
                        'Resources/templates/twig/layout.html.twig'    => 'templates/layout/default.html.twig',
                        'Resources/templates/twig/home-page.html.twig' => 'templates/app/home-page.html.twig',
                    ],
                    'modular' => [
                        'Resources/templates/twig/404.html.twig'       => 'src/App/templates/error/404.html.twig',
                        'Resources/templates/twig/error.html.twig'     => 'src/App/templates/error/error.html.twig',
                        'Resources/templates/twig/layout.html.twig'    => 'src/App/templates/layout/default.html.twig',
                        'Resources/templates/twig/home-page.html.twig' => 'src/App/templates/app/home-page.html.twig',
                    ],
                    'minimal' => [
                    ],
                ],
                3 => [
                    'name'     => 'zend-view <comment>installs zend-servicemanager</comment>',
                    'packages' => [
                        'zendframework/zend-expressive-zendviewrenderer',
                    ],
                    'flat' => [
                        'Resources/templates/zend-view/404.phtml'       => 'templates/error/404.phtml',
                        'Resources/templates/zend-view/error.phtml'     => 'templates/error/error.phtml',
                        'Resources/templates/zend-view/layout.phtml'    => 'templates/layout/default.phtml',
                        'Resources/templates/zend-view/home-page.phtml' => 'templates/app/home-page.phtml',
                    ],
                    'modular' => [
                        'Resources/templates/zend-view/404.phtml'       => 'src/App/templates/error/404.phtml',
                        'Resources/templates/zend-view/error.phtml'     => 'src/App/templates/error/error.phtml',
                        'Resources/templates/zend-view/layout.phtml'    => 'src/App/templates/layout/default.phtml',
                        'Resources/templates/zend-view/home-page.phtml' => 'src/App/templates/app/home-page.phtml',
                    ],
                    'minimal' => [
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
