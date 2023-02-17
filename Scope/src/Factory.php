<?php
namespace Phalconeer\Scope;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Scope as This;

class Factory extends Bootstrap\Factory
{
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        return new This\Bo\ScopeBo(
            $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME)
        );
    }
}