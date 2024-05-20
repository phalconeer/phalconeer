<?php
namespace Phalconeer\ErrorHandler\Listener;

use Phalconeer\ErrorHandler as This;
use Phalconeer\ExceptionListener;
use Phalconeer\Id;
use Phalconeer\LiveSession;
use Phalconeer\Middleware;

class ErrorHandler
{
    protected $handlerChain = null;

    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession,
        protected array $handlers
    )
    {

    }

    public function handlePhpError(
        int $errno,
        string $errstr,
        ?string $errfile = null,
        ?int $errline = null,
    )
    {
        $errPieces = explode('Stack trace:', $errstr);

        $exportError = This\Data\Error::fromArray([
            'errno'             => $errno,
            'file'              => $errfile,
            'globals'           => [
                'request'           => $_REQUEST,
//                'server'  => $_SERVER,  //TODO: filter which server variables can be exported
                'session'           => $this->liveSession->getSession(), // TODO: how to pass the token?
            ],
            'id'                => Id\Helper\ReadableIdHelper::getId(),
            'line'              => $errline,
            'message'           => $errPieces[0],
            'server'            => ExceptionListener\Helper\TraceHelper::getServerAddress(),
            'trace'             => array_key_exists(1, $errPieces) ? explode(PHP_EOL, $errPieces[1]) : null
            // 'product'   => PRODUCT,
            // 'application'   => Phalcon\Di::getDefault()->get('config')->application->name,
        ]);

        $handlerChain = Middleware\Helper\MiddlewareHelper::createChain(
            Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer($this->handlers),
            function () use ($exportError) {
                if ($exportError->errno() == E_ERROR ) {
                    echo 'INTERNAL SERVER ERROR' . PHP_EOL . PHP_EOL . $exportError->id();
                    exit();
                }
            },
            This\ErrorHandlerInterface::class
        );

        $handlerChain($exportError);
    }

    public function checkForFatal()
    {
        $error = error_get_last();
        if ( !is_null($error)
            && $error["type"] == E_ERROR )
            $this->handlePhpError( $error["type"], $error["message"], $error["file"], $error["line"] );
    }
}
