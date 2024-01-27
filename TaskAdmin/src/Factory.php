<?php
namespace Phalconeer\TaskAdmin;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskAdmin as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskAdmin';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        TaskRegistry\Factory::MODULE_NAME,
    ];
    
    protected static array $configFiles = [
        __DIR__ . '/_config/task_admin_config.php'
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $di = $this->di;

        return function (TaskRegistry\TaskDaoInterface $adapter) use ($config, $di) {
            return new This\Bo\TaskAdminBo(
                $adapter,
                $config,
                $di->get(TaskRegistry\Factory::MODULE_NAME)
            );
        };
    }
}
