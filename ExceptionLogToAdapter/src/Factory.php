<?php
namespace Phalconeer\ExceptionLogToAdapter;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ExceptionLogToAdapter as This;
use Phalconeer\ExceptionListener;
use Phalconeer\Middleware;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'exceptionLogToAdapter';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        ExceptionListener\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
        'request'
    ];

    protected function configure()
    {
        $di = $this->di;
        $exceptionDescriptors = $this->di->get(Config\Factory::MODULE_NAME)
            ->get('exceptionDescriptors', Config\Helper\ConfigHelper::$dummyConfig);

        return function (string $adapterName) use ($di, $exceptionDescriptors) {
            return new This\Bo\ExceptionLogToAdapterBo(
                $di->get($adapterName),
                $exceptionDescriptors
            );
        };
    }
}