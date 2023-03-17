<?php
namespace Phalconeer\ExceptionListener\Bo;


use Phalconeer\Exception;
use Phalconeer\ExceptionListener as This;
use Phalcon;
use Phalcon\Events;
use Phalcon\Dispatcher;
use Phalcon\Mvc;

class ExceptionListener
{
    /**
     * Caught exceptions arrive here.
     */
    public function beforeException(
        Events\Event $event,
        Dispatcher\DispatcherInterface $dispatcher,
        \Exception $exception)
    {
        $exportException = Exception\Export\Exception::fromException($exception);

        if (!is_null($previous = $exception->getPrevious())) {
            $exportException = $exportException->setPrevious(
                Exception\Export\Exception::fromException($previous)
            );
        }


        /**
         * @var Mvc\Dispatcher  $dispatcher
         */
        $dispatcher->getDI()->get(This\Factory::MODULE_NAME, [$dispatcher, $exportException]);

        return false;
    }