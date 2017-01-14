<?php

return [
    'dependencies' => [
        'factories' => [
            Twig_Environment::class => Zend\Expressive\Twig\TwigEnvironmentFactory::class,

            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Twig_Environment::class =>
                Zend\Expressive\Twig\TwigEnvironmentFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\Twig\TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
    ],

    'twig' => [
        'cache_dir'      => 'data/cache/twig',
        'assets_url'     => '/',
        'assets_version' => null,
        'extensions'     => [
            // extension service names or instances
        ],
        'runtime_loaders' => [
            // runtime loader names or instances
        ],
        'globals' => [
            // Variables to pass to all twig templates
        ],
        // 'timezone' => 'default timezone identifier; e.g. America/Chicago',
    ],
];
