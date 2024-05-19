<?php
namespace Phalconeer\ErrorHandler;

use Phalconeer\ErrorHandler as This;
use Phalconeer\Middleware;

interface ErrorHandlerInterface extends Middleware\MiddlewareInterface
{
    public function handle(
        This\Data\Error $error,
        callable $next
    ): ?bool;
}