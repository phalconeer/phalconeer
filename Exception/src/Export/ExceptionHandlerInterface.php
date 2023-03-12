<?php
namespace Phalconeer\Exception\Export;

use Phalconeer\Middleware;
use Phalconeer\Exception;

interface ExceptionHandlerInterface extends Middleware\MiddlewareInterface
{
    public function handle(
        Exception\Export\Exception $exception,
        callable $next
    ): ?bool;
}