<?php
namespace Phalconeer\ExceptionLogToAdapter\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Dispatcher;
use Phalcon\Events;
use Phalconeer\ExceptionListener;

class ExceptionLogToAdapterBo
{
    public function __construct(
        protected array $adapters,
        protected PhalconConfig\Config $exceptionDescriptors
    )
    {
    }

    public function beforeException(
        Events\Event $event,
        Dispatcher\DispatcherInterface $dispatcher,
        \Exception $exception)
    {
        $exceptionToExport = ExceptionListener\Data\Exception::fromException($exception);

        if ($this->exceptionDescriptors->has($exceptionToExport->code())) {
            $errorDetails = $this->exceptionDescriptors->get($exceptionToExport->code());
            $exceptionToExport = ExceptionListener\Data\Exception::fromArray([
                'id'            => $exceptionToExport->id(),
                'code'          => $exceptionToExport->code(),
                'statusCode'    => $errorDetails->statusCode,
                'message'       => $errorDetails->message,
            ]);
        }

        foreach ($this->adapters as $adapter) {
            $adapter->save($exceptionToExport);
        }
    }
}