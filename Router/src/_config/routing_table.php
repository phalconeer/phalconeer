<?php
use Phalconeer\Router\Helper\RouterHelper as RH;

return [
    'index' => [
        RH::ROUTE           => '/',
        RH::PARAMETERS      => [
            RH::CONTROLLER      => 'index',
            RH::ACTION          => 'index'
        ],
    ],
    'resource' => [
        RH::MODULE          => 'resource',
        RH::CONTROLLER      => 'resource',
        RH::ROUTES          => [
            'list'          => [ // This can be referenced by resource.list
                RH::ROUTE           => '/list',
                RH::PARAMETERS      => [
                    RH::ACTION          => 'list'
                ]
            ]
        ]
    ]
];