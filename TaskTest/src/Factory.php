<?php
namespace Phalconeer\TaskTest;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Task;
use Phalconeer\TaskTest as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskTest';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Task\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/task_config.php'
    ];

    protected function configure()
    {
        return new This\Bo\TaskTestBo();
    }
}