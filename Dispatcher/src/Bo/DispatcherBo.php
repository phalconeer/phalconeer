<?php
namespace Phalconeer\Dispatcher\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Dispatcher;
use Phalcon\Events;

class DispatcherBo
{
    public function __construct(
        protected Dispatcher\DispatcherInterface $dispatcher,
        protected PhalconConfig\Config $applicationConfig,
        protected \ArrayObject $eventListeners = new \ArrayObject(),
    )
    {
        $this->configureDispatcher();
    }

    protected function configureDispatcher()
    {
        $this->dispatcher->setDefaultNamespace($this->applicationConfig->defaultNamespace);
        if ($this->eventListeners->count() > 0) {
            $eventsManager = new Events\Manager();
            $iterator = $this->eventListeners->getIterator();
            while ($iterator->valid()) {
                $eventIterator = $iterator->current()->getIterator();
                while ($eventIterator->valid()) {
                    $eventsManager->attach($iterator->key(), $eventIterator->current());
                    $eventIterator->next();
                }
                $iterator->next();
            }
            $this->dispatcher->setEventsManager($eventsManager);
        }
    }

    public function getDispatcher() : Dispatcher\DispatcherInterface
    {
        return $this->dispatcher;
    }
}
