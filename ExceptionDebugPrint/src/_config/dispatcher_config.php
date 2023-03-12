<?php
use Phalconeer\ExceptionDebugPrint as This;

return [
    'dispatcher'        => [
        'eventListeners'    => [
            'dispatch:beforeException' => [
                This\Factory::MODULE_NAME => This\Factory::MODULE_NAME  //Make sure there can be more listeners
            ]
        ]
    ],
];