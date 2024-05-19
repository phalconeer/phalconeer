<?php
namespace Phalconeer\ErrorToExceptionHandler;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ErrorToExceptionHandler as This;
use Phalconeer\ExceptionListener;
use Phalconeer\LiveSession;
use Phalconeer\Middleware;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'errorHandler';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        ExceptionListener\Factory::MODULE_NAME,
        LiveSession\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/error_handler_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);

        $handler = new This\Listener\ErrorToExceptionHandler();

        set_error_handler([$handler, 'handlePhpError'], $config->get('levels', E_ALL));

        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}