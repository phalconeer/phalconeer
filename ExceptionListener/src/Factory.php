<?php
namespace Phalconeer\ExceptionListener;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Exception;
use Phalconeer\ExceptionListener as This;
use Phalconeer\Middleware;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'exceptionListener';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/exception_listener_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->exception;

        $handlers = [];
        $iterator = $config->get('handlers', Config\Helper\ConfigHelper::$dummyConfig)->getIterator();
        while ($iterator->valid()) {
            $handlers[] = $this->di->get($iterator->key(), [$iterator->current()]);
            $iterator->next();
        }

        $handlerChain = Middleware\Helper\MiddlewareHelper::createChain(
            Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer($handlers),
            function () {},
            Exception\Export\ExceptionHandlerInterface::class
        );

        return function (This\Data\Exception $exception) use ($handlerChain) {
            $handlerChain($exception);
        };
    }
}