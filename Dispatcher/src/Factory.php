<?php

namespace Phalconeer\Dispatcher;

use Phalcon\Mvc;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Dispatcher as This;

/**
 * Initializes the dispatcher.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'dispatcher';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];
    
    protected function configure() : Mvc\DispatcherInterface
    {
        $dispatcherBo = new This\Bo\DispatcherBo(
            new Mvc\Dispatcher(),
            $this->di->get(Config\Factory::MODULE_NAME)
        );
        return $dispatcherBo->getDispatcher();
    }
}
