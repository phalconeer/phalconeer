<?php

namespace Phalconeer\DispatcherCli;

use Phalcon;
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
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get('dispatcher', new Phalcon\Config\Config());
        $dispatcherBo = new This\Bo\DispatcherCliBo(
            new Cli\Dispatcher(),
            $config
        );
        return $dispatcherBo->getDispatcher();
    }
}
