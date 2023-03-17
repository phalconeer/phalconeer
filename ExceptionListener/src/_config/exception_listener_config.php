<?php
use Phalconeer\ExceptionListener as This;

return [
    'dispatcher'    => [
        'eventListeners'    => [
            'dispatch:beforeException' => This\Listener\ExceptionListener::class
        ]
    ],
    'application'   => [
        'errorController'       => 'error',
        'errorNotFoundAction'   => 'error404'
    ],
    'exception'     => [
        'handlers'              => [
            'exceptionDebugPrint'       => []
        ]
    ]
];