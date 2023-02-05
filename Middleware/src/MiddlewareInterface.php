<?php
namespace Phalconeer\Middleware;

interface MiddlewareInterface
{
    public function getActionName() : string;
}