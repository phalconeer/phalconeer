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
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get('dispatcher', Config\Helper\ConfigHelper::$dummyConfig);
        $applicationConfig = $this->di->get(Config\Factory::MODULE_NAME)->get('application', Config\Helper\ConfigHelper::$dummyConfig);
        $dispatcherBo = new This\Bo\DispatcherBo(
            new Mvc\Dispatcher(),
            $config,
            $applicationConfig
        );
        return $dispatcherBo->getDispatcher();
    }
}
