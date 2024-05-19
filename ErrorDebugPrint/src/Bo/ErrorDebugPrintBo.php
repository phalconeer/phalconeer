<?php
namespace Phalconeer\ErrorDebugPrint\Bo;

use Phalconeer\ErrorHandler;
use Phalcon\Http;

class ErrorDebugPrintBo implements ErrorHandler\ErrorHandlerInterface
{
    public function __construct(
        protected Http\Request $request,
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
        if (DEBUG_ON
            && $this->request->hasHeader('X-Debug')) {

            echo \Phalconeer\Dev\TVarDumper::dump($error);
            exit();
        }
        $next($error);
        return false;
    }

}