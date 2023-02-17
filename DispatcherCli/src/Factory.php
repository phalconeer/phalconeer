<?php

namespace Phalconeer\DispatcherCli;

use Phalcon\Cli;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\DispatcherCli as This;

/**
 * Initializes the dispatcher.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'dispatcher';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(
            static::MODULE_NAME,
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $dispatcherBo = new This\Bo\DispatcherCliBo(
            new Cli\Dispatcher(),
            $config
        );
        return $dispatcherBo->getDispatcher();
    }
}
