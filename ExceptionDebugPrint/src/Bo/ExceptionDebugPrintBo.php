<?php
namespace Phalconeer\ExceptionDebugPrint\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Dispatcher;
use Phalcon\Events;
use Phalcon\Http;
use Phalconeer\Exception;
use Phalconeer\Middleware;

class ExceptionDebugPrintBo
{
    public function __construct(
        protected Http\Request $request,
        protected PhalconConfig\Config $exceptionDescriptors
    )
    {
    }

    public function beforeException(
        Events\Event $event,
        Dispatcher\DispatcherInterface $dispatcher,
        \Exception $exception)
    {
        if (DEBUG_ON
            && $this->request->hasHeader('X-Debug')) {
            $exceptiontoExport = Exception\Export\Exception::fromException($exception);

            if ($this->exceptionDescriptors->has($exceptiontoExport->code())) {
                echo 'VISIBLE ERROR MESSAGE'. PHP_EOL;
                echo \Phalconeer\Dev\TVarDumper::dump($this->exceptionDescriptors->get($exceptiontoExport->code())) . PHP_EOL . PHP_EOL;
            } else {
                echo 'NO ERROR MESSAGE SET!' . PHP_EOL . PHP_EOL;
            }
            echo \Phalconeer\Dev\TVarDumper::dump($exceptiontoExport);
            die();
        }
        return false;
    }
}