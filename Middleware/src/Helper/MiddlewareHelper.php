<?php
namespace Phalconeer\Middleware\Helper;

use Phalconeer\Middleware as This;

class MiddlewareHelper
{
    /**
     * Creates a middleware call chain, where $nextChainElement will be called last
     */
    public static function createChain(
        \SplDoublyLinkedList $middlewares,
        callable $finalChainElement,
        string $interfaceCheck = null
    ) {
        $nextChainElement = $finalChainElement;
        $middlewares->rewind();
        iterator_apply(
            $middlewares,
            function ($iterator) use ($interfaceCheck, &$nextChainElement, $finalChainElement) {
                $middleware = $iterator->current();
                if (!$middleware instanceof This\MiddlewareInterface) {
                    throw new This\Exception\InvalidMiddlewareException(
                        get_class($middleware),
                        This\Helper\ExceptionHelper::MIDDLEWARE__INVALID_MIDDLEWARE_CLASS
                    );
                }
                if (!is_null($interfaceCheck)
                    && !$middleware instanceof $interfaceCheck) {
                    throw new This\Exception\InvalidHandlerException(
                        get_class($middleware) . ' !instanceof' . $interfaceCheck,
                        This\Helper\ExceptionHelper::MIDDLEWARE__INVALID_HANDLER_FUNCTION);
                }
                $currentCall = function() use ($middleware, $nextChainElement, $finalChainElement) {
                    $arguments = func_get_args();
                    $lastArg = end($arguments);
                    if (is_object($lastArg)
                        && $lastArg instanceof This\Data\TerminateMiddleware) {
                        return call_user_func_array($finalChainElement, $arguments);
                    }
                    $arguments[] = $nextChainElement;
                // echo get_class($middleware) . PHP_EOL;
                    return call_user_func_array([$middleware, $middleware->getActionName()], $arguments);
                };

                $nextChainElement = $currentCall;
                $iterator->next();
                return true;
            },
            [$middlewares]
        );

        return $nextChainElement;
    }

    /**
     * Creates an iterable middleware container
     */
    public static function createMiddlewaresContainer(
        array $middlewares = [],
        bool $reverse = false
    ) : \SplDoublyLinkedList
    {
        if ($reverse) {
            $container = new \SplDoublyLinkedList();
        } else {
            $container = new \SplStack();

        }

        array_map(function ($middleware) use ($container) {
            $container->offsetSet(null, $middleware);
        }, $middlewares);

        return $container;
    }
}