<?php
namespace ExceptionForwardToController\Controller;

use Phalconeer\ExceptionListener;

class ErrorController
{
    public function exceptionAction(ExceptionListener\Data\Exception $exception)
    {
        echo 'This is a dummy solution ' . $exception->id();
    }
}