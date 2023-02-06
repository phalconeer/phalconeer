<?php
namespace Phalconeer\DispatcherCli\Bo;

use Phalcon\Config;
use Phalcon\Cli;
use Phalcon\Events;

class DispatcherCliBo
{
    protected Config\Config $config;

    protected Cli\Dispatcher $dispatcher;

    public function __construct(Cli\DispatcherInterface $dispatcher, Config\Config $config)
    {
        $this->dispatcher = $dispatcher;
        $this->config = $config;

        $this->configureDispatcher();
    }

    protected function configureDispatcher()
    {
        $this->dispatcher->setDefaultNamespace($this->config->application->defaultNamespace);
        if ($this->config->dispatcher->offsetExists('eventListeners')) {
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
