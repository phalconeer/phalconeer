<?php
namespace Phalconeer\TaskRegistry;

use Phalconeer\Bootstrap;
use Phalconeer\TaskRegistry as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskRegistry';
    
    protected function configure()
    {
        return new This\Bo\TaskRegistryBo();
    }
}
