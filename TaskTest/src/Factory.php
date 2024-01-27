<?php
namespace Phalconeer\TaskTest;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskTest as This;

class Factory extends Bootstrap\Factory
{
    use TaskRegistry\Trait\RegisterTask;

    const MODULE_NAME = 'taskTest';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        TaskRegistry\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/task_config.php'
    ];
    
    protected function configure()
    {
        return $this->registerTask(static::MODULE_NAME, This\Bo\TaskTestBo::class);
    }
}