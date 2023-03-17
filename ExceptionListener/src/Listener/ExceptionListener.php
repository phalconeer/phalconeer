<?php
namespace Phalconeer\ExceptionListener\Listener;


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

    /**
     *
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     * @param type $errcontext
     * @param Di $di
     */
    public function handlePhpError($errno, $errstr, $errfile, $errline, $errcontext, Phalcon\Di $di)
    {
        $exportDto = new ExportErrorDto(array(
            'id'      => null,
            'errno'   => $errno,
            'message' => $errstr,
            'file'    => $errfile,
            'line'    => $errline,
            'context' => json_encode($errcontext),
            'product'   => PRODUCT,
            'application'   => Phalcon\Di::getDefault()->get('config')->application->name,
            'server'  => $this->getServerAddress(),
            'globals' => [
                'request' => ArrayHelper::extractForElastic($_REQUEST),
//                'server'  => ArrayHelper::extractForElastic($_SERVER),
                'session' => ArrayHelper::extractForElastic($this->collectSessionVars()),
            ],
            'runId'   => $this->getRunId(),
        ));
        $this->logError($exportDto);
        $this->printError($di['dispatcher'], $exportDto);
    }
}
