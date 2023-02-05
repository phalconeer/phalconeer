<?php
namespace Phalconeer\Middleware\Test\Mock;

use Phalconeer\Middleware\Test as This;
use Phalconeer\Middleware;

class TrackerMiddlewareMock extends Middleware\Bo\DefaultMiddleware  implements This\Mock\TrackerInterface
{
    const TARGET = 'middleware1';

    function handle(\ArrayObject $tracker, callable $next)
    {
        $tracker->offsetSet(null, self::TARGET);
        $next($tracker);
    }
}