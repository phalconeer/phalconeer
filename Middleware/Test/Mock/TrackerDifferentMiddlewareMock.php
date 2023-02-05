<?php
namespace Phalconeer\Middleware\Test\Mock;

use Phalconeer\Middleware;

class TrackerDifferentMiddlewareMock extends Middleware\Bo\DefaultMiddleware
{
    const TARGET = 'different1';

    function handle(\ArrayObject $tracker, callable $next)
    {
        $tracker->offsetSet(null, self::TARGET);
        $next($tracker);
    }
}