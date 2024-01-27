<?php
namespace Phalconeer\Task;

use Phalconeer\Bootstrap;
use Phalconeer\Task as This;
use Phalconeer\TaskRegistry;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'task';
    
    protected function configure()
    {
        return function (TaskRegistry\TaskDaoInterface $adapter) {

            return new This\Bo\TaskBo(
                $adapter,
            );
        };
    }
}
