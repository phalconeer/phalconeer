<?php
return [
    'http'      => [
        'namespaces'    => [
            'Psr\Http\Message'  => APPLICATION_PATH . '/vendor/psr/http-factory/src',
            'Psr\Http\Client'   => APPLICATION_PATH . '/vendor/psr/http-client/src',
            'Nyholm\Psr7'       => APPLICATION_PATH . '/vendor/nyholm/psr7/src',
        ]
    ]
];