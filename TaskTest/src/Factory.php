<?php
namespace Phalconeer\TaskTest;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskTest as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskTest';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        TaskRegistry\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/task_config.php'
    ];

    protected function registerTask()
    {
        $taskRegistry = $this->di->get(TaskRegistry\Factory::MODULE_NAME);
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $listenerConfig = TaskRegistry\Data\ListenerConfig::fromArray($config->toArray());
        $task = new This\Bo\TaskTestBo($listenerConfig);
        $taskRegistry->registerTask(
            $task,
            $listenerConfig
        );
        return $task;
    }

    protected function configure()
    {
        return $this->registerTask();
    }
}