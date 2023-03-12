<?php
namespace Phalconeer\ExceptionForwardToController\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Dispatcher;
use Phalcon\Events;
use Phalconeer\Exception;
use Phalconeer\ExceptionForwardToController as This;

class ExceptionForwardToControllerBo
{
    public function __construct(
        protected PhalconConfig\Config $config,
        protected PhalconConfig\Config $exceptionDescriptors
    )
    {
    }

    public function beforeException(
        Events\Event $event,
        Dispatcher\DispatcherInterface $dispatcher,
        \Exception $exception)
    {
        $exceptionToExport = Exception\Export\Exception::fromException($exception);

        if ($this->exceptionDescriptors->has($exceptionToExport->code())) {
            $errorDetails = $this->exceptionDescriptors->get($exceptionToExport->code());
            $exceptionToExport = Exception\Export\Exception::fromArray([
                'id'            => $exceptionToExport->id(),
                'code'          => $exceptionToExport->code(),
                'statusCode'    => $errorDetails->statusCode,
                'message'       => $errorDetails->message,
            ]);
        }

        $dispatcher->forward([
            'namespace'  => $this->config->get('namespace', ''),
            'controller' => $this->config->get('controller', This\Helper\ExceptionForwardToControllerHelper::DEFAULT_CONTROLLER),
            'action'     => $this->config->get('action', This\Helper\ExceptionForwardToControllerHelper::DEFAULT_ACTION),
            'params'     => [
                $exceptionToExport
            ]
        ]);
    }
}