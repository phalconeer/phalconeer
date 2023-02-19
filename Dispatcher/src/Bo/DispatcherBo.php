<?php
namespace Phalconeer\Dispatcher\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Mvc;
use Phalcon\Events;

class DispatcherBo
{
    public function __construct(
        protected Mvc\DispatcherInterface $dispatcher,
        protected PhalconConfig\Config $config,
        protected PhalconConfig\Config $applicationConfig,
    )
    {
        $this->configureDispatcher();
    }

    protected function configureDispatcher()
    {
        $this->dispatcher->setDefaultNamespace($this->applicationConfig->defaultNamespace);
        if ($this->config->has('eventListeners')) {
            $eventsManager = new Events\Manager();
            foreach ($this->config->eventListeners as $event => $listener) {
                $eventsManager->attach($event, new $listener);
            }
            $this->dispatcher->setEventsManager($eventsManager);
        }
    }

    public function getDispatcher() : Mvc\DispatcherInterface
    {
        return $this->dispatcher;
    }
}
