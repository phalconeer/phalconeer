<?php
use Phalconeer\ExceptionLogToAdapter as This;

return [
    'dispatcher'        => [
        'eventListeners'    => [
            'dispatch:beforeException' => [
                This\Factory::MODULE_NAME => This\Factory::MODULE_NAME,
            ]
        ]
    ],
];