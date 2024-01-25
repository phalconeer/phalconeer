<?php
namespace Phalconeer\RouterCli;

use Phalconeer\Bootstrap;
use Phalconeer\RouterCli as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'router';
    
    protected static array $requiredModules = ['config'];

    protected function configure()
    {
        return new This\Bo\RouterCliBo();
    }
}