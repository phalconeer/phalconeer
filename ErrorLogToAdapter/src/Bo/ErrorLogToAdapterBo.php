<?php
namespace Phalconeer\ErrorLogToAdapter\Bo;

use Phalconeer\ErrorHandler;

class ErrorLogToAdapterBo implements ErrorHandler\ErrorHandlerInterface
{
    public function __construct(
        protected array $adapters,
    )
    {
    }

    public function getActionName() : string
    {
        return 'handle';
    }

    public function handle(
        ErrorHandler\Data\Error $error,
        callable $next
    ) : ?bool
    {
        foreach ($this->adapters as $adapter) {
            $adapter->save($error);
        }

        $next($error);
        return false;
    }
}