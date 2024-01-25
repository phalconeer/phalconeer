<?php
namespace Phalconeer\DispatcherCli\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Cli;
use Phalcon\Events;

class DispatcherCliBo
{

    public function __construct(
        protected Cli\DispatcherInterface $dispatcher,
        protected PhalconConfig\Config $config)
    {
        $this->configureDispatcher();
    }

    protected function configureDispatcher()
    {
        $this->dispatcher->setDefaultNamespace($this->config->defaultNamespace);
        if ($this->config?->dispatcher?->offsetExists('eventListeners')) {
            $eventsManager = new Events\Manager();
            foreach ($this->config->dispatcher->eventListeners as $event => $listener) {
                $eventsManager->attach($event, new $listener);
            }
            $this->dispatcher->setEventsManager($eventsManager);
        }
    }

    public function getDispatcher() : Cli\DispatcherInterface
    {
        return $this->dispatcher;
    }
}
