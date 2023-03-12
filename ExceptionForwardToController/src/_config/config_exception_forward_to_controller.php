<?php
use Phalconeer\ExceptionForwardToController as This;

return [
    This\Factory::MODULE_NAME       => [
        //These settings will try to call ErrorController::exceptionAction in this namespace
        //Recommnded to create own implementation
        'action'                => 'exception', 
        'controller'            => 'error',
        'namespace'             => 'Phalconeer\ExceptionForwardToController',
    ],
];