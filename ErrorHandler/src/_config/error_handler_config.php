<?php
use Phalconeer\ErrorHandler as This;

return [
    This\Factory::MODULE_NAME       => [
        'errorHandler'          => This\Listener\ErrorHandler::class,
        'handlers'              => [],
        'levels'                => E_RECOVERABLE_ERROR,
    ]
];