<?php
namespace Phalconeer\Task;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Task as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'task';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/task_config.php'
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);

        $handlerConfig = $config->get(This\Helper\TaskHelper::CONFIG_HANDLER, Config\Helper\ConfigHelper::$dummyConfig);
        array_map(
            function ($handlerKey) use ($handlerConfig) {
                $handlerConfig->get($handlerKey)->offsetSet('moduleInstance', $this->di->get($handlerConfig->{$handlerKey}->get('module')));
            },
            array_keys($handlerConfig->toArray()),
        );
        return function (This\TaskDaoInterface $adapter) use ($config) {

            return new This\Bo\TaskBo(
                $adapter,
                $config
            );
        };
    }
}
