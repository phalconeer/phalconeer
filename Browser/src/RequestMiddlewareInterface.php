<?php
namespace Phalconeer\Browser;

use Phalconeer\Http;
use Phalconeer\Middleware;
use Psr;

interface RequestMiddlewareInterface extends Middleware\MiddlewareInterface
{
    public function handleRequest(Psr\Http\Message\RequestInterface | Http\MessageInterface $request, callable $next) : ?bool;
}