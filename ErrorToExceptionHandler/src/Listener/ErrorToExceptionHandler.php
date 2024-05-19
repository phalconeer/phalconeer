<?php
namespace Phalconeer\ErrorToExceptionHandler\Listener;

class ErrorToExceptionHandler
{
    public function handlePhpError(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null,
    )
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
