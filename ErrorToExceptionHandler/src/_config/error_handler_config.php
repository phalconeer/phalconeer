<?php
use Phalconeer\ErrorToExceptionHandler as This;

return [
    This\Factory::MODULE_NAME       => [
        'errorHandler'          => This\Listener\ErrorToExceptionHandler::class,
        'levels'                => E_ALL,
    ]
];