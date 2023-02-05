<?php
namespace Phalconeer\Browser;

use Phalconeer\Middleware\MiddlewareInterface;
use Psr;

interface RequestMiddlewareInterface extends MiddlewareInterface
{
    public function handleRequest(Psr\Http\Message\RequestInterface $request, callable $next) : ?bool;
}