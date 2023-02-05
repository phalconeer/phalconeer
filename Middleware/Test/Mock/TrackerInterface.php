<?php
namespace Phalconeer\Middleware\Test\Mock;

interface TrackerInterface
{
    function handle(\ArrayObject $tracker, callable $next);
}