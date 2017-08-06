<?php

use Doctrine\ORM\EntityManager;
use Kocal\Expressive\Database\Doctrine\EntityManagerFactory;

return [
    'dependencies' => [
        'factories' => [
            // Use EntityManagerFactory for using Doctrine EntityManager:
            EntityManager::class => EntityManagerFactory::class
        ]
    ],

    'doctrine' => [
        // See http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
        //
        // Do not forget to create your database: `./vendor/bin/doctrine orm:schema-tool:create`
        'driver' => 'pdo_mysql',
        'path' => __DIR__ . '/../../database.sqlite',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],

    'entities_path' => [
        __DIR__ . '/../../src/App/Entity'
    ],
];
