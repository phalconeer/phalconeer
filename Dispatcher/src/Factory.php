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
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $applicationConfig = $this->di->get(Config\Factory::MODULE_NAME)->get('application', Config\Helper\ConfigHelper::$dummyConfig);
        $eventListeners = new \ArrayObject();
        if ($config->has('eventListeners')) {
            foreach ($config->get('eventListeners') as $event => $listeners) {
                $listeners = $listeners->toArray();
                if (!is_array($listeners)) {
                    $listeners = [$listeners];
                }
                if (!$eventListeners->offsetExists($event)) {
                    $eventListeners->offsetSet($event, new \ArrayObject());
                }

                $currentEvent = $eventListeners->offsetGet($event);

                foreach ($listeners as $listener) {
                    if ($this->di->offsetExists($listener)) {
                        $instance = $this->di->offsetGet($listener);
                    } else {
                        $instance = new $listener;
                    }
                
                    $currentEvent->offsetSet(null, $instance);
                }
            }
        }
        $dispatcherBo = new This\Bo\DispatcherBo(
            new Mvc\Dispatcher(),
            $applicationConfig,
            $eventListeners
        );

        return $dispatcherBo->getDispatcher();
    }
}
