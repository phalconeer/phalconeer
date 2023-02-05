<?php
namespace Phalconeer\Browser;

use Phalconeer\Middleware\MiddlewareInterface;
use Psr;

interface ResponseMiddlewareInterface extends MiddlewareInterface
{
    public function handleResponse(Psr\Http\Message\ResponseInterface $response, callable $next) : ?bool;
}