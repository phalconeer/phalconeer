<?php
namespace Phalconeer\Browser;

use Psr;
use Phalconeer\Middleware;
use Phalconeer\Http;

interface ResponseMiddlewareInterface extends Middleware\MiddlewareInterface
{
    public function handleResponse(Psr\Http\Message\ResponseInterface | Http\MessageInterface $response, callable $next) : ?bool;
}