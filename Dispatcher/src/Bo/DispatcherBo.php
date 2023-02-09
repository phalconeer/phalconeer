<?php
namespace Phalconeer\Dispatcher\Bo;

use Phalcon\Config;
use Phalcon\Mvc;
use Phalcon\Events;

class DispatcherBo
{
    public function __construct(
        protected Mvc\DispatcherInterface $dispatcher,
        protected Config\Config $config,
        protected Config\Config $applicationConfig,
    )
    {
        $this->dispatcher = $dispatcher;
        $this->config = $config;

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
