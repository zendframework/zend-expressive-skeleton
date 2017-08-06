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
        // DBAL configuration. More at http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
        'driver' => 'pdo_mysql',
        'dbname' => 'mydb',
        'user' => 'user',
        'password' => 'secret',
        'host' => 'localhost',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],

    'entities_path' => [
        __DIR__ . '/../../src/App/Entity'
    ],
];