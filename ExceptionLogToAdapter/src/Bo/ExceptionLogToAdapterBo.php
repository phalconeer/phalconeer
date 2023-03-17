<?php
namespace Phalconeer\ExceptionLogToAdapter\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Exception;
use Phalconeer\ExceptionLogToAdapter as This;
use Phalconeer\Middleware;

class ExceptionLogToAdapterBo extends Middleware\Bo\DefaultMiddleware implements Exception\Export\ExceptionHandlerInterface
{
    public function __construct(
        protected This\LogToAdapterInterface $adapter,
        protected PhalconConfig\Config $exceptionDescriptors
    )
    {
    }

    public function handle(
        Exception\Export\Exception $exception,
        callable $next
    ) : ?bool
    {
        $this->adapter->save($exception, $this->exceptionDescriptors);

        $next($exception);
        return null;
    }
}