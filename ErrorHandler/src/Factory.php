<?php
namespace Phalconeer\ErrorHandler;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ErrorHandler as This;
use Phalconeer\LiveSession;
use Phalconeer\Middleware;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'errorHandler';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        LiveSession\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/error_handler_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $handlers = [];
        $iterator = $config->get('handlers', Config\Helper\ConfigHelper::$dummyConfig)->getIterator();
        while ($iterator->valid()) {
            $handlers[] = $this->di->get($iterator->key(), [$iterator->current()]);
            $iterator->next();
        }

        $handler = new This\Listener\ErrorHandler(
            $this->di->get(LiveSession\Factory::MODULE_NAME),
            $handlers,
        );

        set_error_handler([$handler, 'handlePhpError'], $config->get('levels', E_RECOVERABLE_ERROR));
        register_shutdown_function([$handler, 'checkForFatal']);

        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}