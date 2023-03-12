<?php
namespace ExceptionForwardToController\Controller;

use Phalconeer\Exception;

class ErrorController
{
    public function exceptionAction(Exception\Export\Exception $exception)
    {
        echo 'This is a dummy solution ' . $exception->id();
    }
}