<?php
namespace Phalconeer\Dispatcher\Bo;

use Phalcon\Config;
use Phalcon\Mvc;
use Phalcon\Events;

class DispatcherBo
{
    protected Mvc\Dispatcher $dispatcher;

    protected Config\Config $config;

    protected function configureDispatcher()
    {
        $this->dispatcher->setDefaultNamespace($this->config->application->defaultNamespace);
        if ($this->config->has('dispatcher') 
            && $this->config->dispatcher->has('eventListeners')) {
            $eventsManager = new Events\Manager();
            foreach ($this->config->dispatcher->eventListeners as $event => $listener) {
                $eventsManager->attach($event, new $listener);
            }
            $this->dispatcher->setEventsManager($eventsManager);
        }
    }

    public function __construct(
        Mvc\DispatcherInterface $dispatcher,
        Config\Config $config
    )
    {
        $this->dispatcher = $dispatcher;
        $this->config = $config;

        $this->configureDispatcher();
    }

    public function getDispatcher() : Mvc\DispatcherInterface
    {
        return $this->dispatcher;
    }
}
