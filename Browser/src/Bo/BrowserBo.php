<?php
namespace Phalconeer\Browser\Bo;

use Phalconeer\Browser as This;
use Phalconeer\CurlClient;
use Phalconeer\Http;
use Phalconeer\Middleware;
use Psr;

class BrowserBo implements This\BrowserInterface
{
    protected CurlClient\Bo\CurlClientBo $client;

    protected \SplDoublyLinkedList $requestMiddlewares;

    protected \SplDoublyLinkedList $responseMiddlewares;

    /**
     * The constructor.
     *
     */
    public function __construct(
        CurlClient\CurlClientInterface $client,
        array $requestMiddlewares = [],
        array $responseMiddlewares = []
    )
    {
        $this->client = $client;
        $this->requestMiddlewares = Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer($requestMiddlewares);
        $this->responseMiddlewares = Middleware\Helper\MiddlewareHelper::createMiddlewaresContainer($responseMiddlewares);
        //TODO: Implement API cache here
    }

    public function addRequestMiddleware(This\RequestMiddlewareInterface $middleware)
    {
        $this->requestMiddlewares->offsetSet(null, $middleware);
    }

    public function addResponseMiddleware(This\ResponseMiddlewareInterface $middleware)
    {
        $this->responseMiddlewares->offsetSet(null, $middleware);
    }

    // public function handleResponse(Psr\Http\Message\ResponseInterface $response)
    // {
    //     return $response;
    // }

    protected function resetClientOptions()
    {
        $this->client->resetOptions();
    }

    protected function setClientOptions()
    {
        //TODO: provide some basic options via array / ArrayObject
        return;
    }

    public function call(Psr\Http\Message\RequestInterface | Http\Data\Request $request) : Http\MessageInterface
    {
        $this->resetClientOptions();
        $this->setClientOptions();
        $response = new CurlClient\Data\CurlResponse(
            new Http\Data\Response(new \ArrayObject([
                'requestId'         => $request->requestId()
            ]))
        );
        $requestHandlerChain = Middleware\Helper\MiddlewareHelper::createChain(
            $this->requestMiddlewares,
            function (Psr\Http\Message\RequestInterface $request) use (&$response) {
                $response = $this->client->sendRequest($request, $response);
            },
            This\RequestMiddlewareInterface::class
        );

        $requestHandlerChain($request);

        $responseHandlerChain = Middleware\Helper\MiddlewareHelper::createChain(
            $this->responseMiddlewares,
            function (Psr\Http\Message\ResponseInterface $responseTransformed) use (&$response) {
                $response = $responseTransformed;
            },
            This\ResponseMiddlewareInterface::class
        );

        $responseHandlerChain($response);

        return $response;
    }
}