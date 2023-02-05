<?php
namespace Phalconeer\Middleware\Bo;

use Phalconeer\Middleware as This;

abstract class DefaultMiddleware implements This\MiddlewareInterface
{
    /**
     * A function with the name below has to exist in the extended class
     */
    protected static $handlerName = 'handle';

    public function getActionName() : string
    {
        return static::$handlerName;
    }

    /**
     * The last parameter of the middleware functions has to always be the next in the chain
     *
     * @param callable $next
     * @return void
     * public function handle(callable $next)
     * {
     *     return $next();
     * }
     */
}