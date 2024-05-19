<?php
use Phalconeer\ExceptionForwardToController as This;

return [
    'dispatcher'        => [
        'eventListeners'    => [
            'dispatch:beforeException' => [
                This\Factory::MODULE_NAME => This\Factory::MODULE_NAME,
            ]
        ]
    ],
];