<?php
namespace Phalconeer\TaskRunner;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Task;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskRunner as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskRunner';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Task\Factory::MODULE_NAME,
        TaskRegistry\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/task_runner_config.php'
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $di = $this->di;

        return function (TaskRegistry\TaskDaoInterface $adapter, int $limit = 60) use ($config, $di) {
            return new This\Bo\TaskRunnerBo(
                $di->get(TaskRegistry\Factory::MODULE_NAME),
                $di->get(Task\Factory::MODULE_NAME, [$adapter]),
                $limit,
                $config
            );
        };
    }
}