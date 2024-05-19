<?php
namespace Phalconeer\ErrorLogToAdapter;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ErrorLogToAdapter as This;
use Phalconeer\Middleware;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'errorLogToAdapter';

    protected static array $configFiles = [
        __DIR__ . '/_config/dispatcher_config.php',
    ];

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
        'request'
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $adapters = [];
        $iterator = $config->get('adapters', Config\Helper\ConfigHelper::$dummyConfig)->getIterator();
        while ($iterator->valid()) {
            $adapters[] = $this->di->get($iterator->key(), [$iterator->current()]);
            $iterator->next();
        }
        return new This\Bo\ErrorLogToAdapterBo(
            $adapters,
        );
    }
}