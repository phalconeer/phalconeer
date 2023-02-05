<?php
namespace Phalconeer\Middleware\Test\Mock;

use Phalconeer\Middleware\Test as This;
use Phalconeer\Middleware;

class TerminateMiddlewareMock extends Middleware\Bo\DefaultMiddleware  implements This\Mock\TrackerInterface
{
    const TARGET = 'terminated';

    function handle(\ArrayObject $tracker, callable $next)
    {
        $tracker->offsetSet(null, self::TARGET);
        $next($tracker, new Middleware\Data\TerminateMiddleware());
    }
}