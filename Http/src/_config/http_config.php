<?php
use Phalconeer\Http as This;

return [
    This\Factory::MODULE_NAME      => [
        'namespaces'    => [
            'Psr\Http\Message'  => APPLICATION_PATH . '/vendor/psr/http-message/src',
            'Psr\Http\Client'   => APPLICATION_PATH . '/vendor/psr/http-client/src',
            'Nyholm\Psr7'       => APPLICATION_PATH . '/vendor/nyholm/psr7/src',
        ]
    ]
];