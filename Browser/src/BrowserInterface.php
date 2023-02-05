<?php
namespace Phalconeer\Browser;

use Phalconeer\Browser as This;
use Psr;

interface BrowserInterface
{
    public function addRequestMiddleware(This\RequestMiddlewareInterface $middleware);

    public function addResponseMiddleware(This\ResponseMiddlewareInterface $middleware);

    public function call(Psr\Http\Message\RequestInterface $request);
}