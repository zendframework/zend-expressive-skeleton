<?php

namespace App;

class ApplicationConfig implements ConfigProviderInterface
{
    public function getConfig()
    {
        return [
            'routes' => [
                [
                    'name' => 'home',
                    'path' => '/',
                    'middleware' => \App\Action\HomePageAction::class,
                    'allowed_methods' => ['GET'],
                ],
                [
                    'name' => 'api.ping',
                    'path' => '/api/ping',
                    'middleware' => \App\Action\PingAction::class,
                    'allowed_methods' => ['GET'],
                ],
            ],
        ];
    }
}
