<?php
namespace Phalconeer\TaskRegistry\Trait;

use Phalconeer\Config;
use Phalconeer\TaskRegistry as This;

trait RegisterTask
{
    protected function getModuleConfig(string $moduleName)
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get($moduleName, Config\Helper\ConfigHelper::$dummyConfig);
        return This\Data\ListenerConfig::fromArray($config->toArray());
    }

    protected function registerTask(string $moduleName, string $listenerClass)
    {
        $taskRegistry = $this->di->get(This\Factory::MODULE_NAME);
        $listenerConfig = $this->getModuleConfig($moduleName);
        $task = new $listenerClass($listenerConfig);
        $taskRegistry->registerTask(
            $task,
            $listenerConfig
        );
        return $task;
    }
}