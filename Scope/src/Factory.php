<?php
namespace Phalconeer\Scope;

use Phalconeer\Bootstrap;
use Phalconeer\AuthMethod;
use Phalconeer\Config;
use Phalconeer\Scope as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'scope';

    protected static array $requiredModules = [
        AuthMethod\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        return new This\Bo\ScopeBo(
            $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME)
        );
    }
}