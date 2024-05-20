<?php
use Phalconeer\ErrorHandler as This;

return [
    This\Factory::MODULE_NAME       => [
        'errorHandler'          => This\Listener\ErrorHandler::class,
        'handlers'              => [],
        'levels'                =>
            E_ERROR + 
            E_WARNING +
            E_NOTICE + 
            E_USER_ERROR +
            E_USER_WARNING + 
            E_USER_NOTICE +
            E_STRICT + 
            E_RECOVERABLE_ERROR,
    ]
];