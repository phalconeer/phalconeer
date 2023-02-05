<?php
namespace Phalconeer\Browser\Bo;

use Phalconeer\Browser as This;
use Phalconeer\CurlClient;
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

    protected function getCacheKey(string $route) : string
    {
        return implode('_', [
            'api_cache',
            $this->getMethod(),
            $route,
            md5(serialize($this->getVariables())),
            md5($this->header->get('Authorization', ''))
        ]);
    }

    protected function getCached(string $route, int $cacheTime = 0) : ?string
    {
        return (is_null($this->cache)) ? null : $this->cache->get($this->getCacheKey($route), $cacheTime);
    }

    protected function setCached(string $route, string $response, int $cacheTime = 0) : void
    {
        if (is_null($this->cache) || $cacheTime = 0) {
            return;
        }

        $this->cache->set(
            $this->getCacheKey($route),
            $response,
            $cacheTime
        );
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

    public function call(Psr\Http\Message\RequestInterface $request) : Psr\Http\Message\ResponseInterface
    {
        $this->resetClientOptions();
        $this->setClientOptions();
        /**
         * @var \Phalconeer\CurlClient\Data\CurlResponse $response
         */
        $response = null;
        $requestHandlerChain = Middleware\Helper\MiddlewareHelper::createChain(
            $this->requestMiddlewares,
            function (Psr\Http\Message\RequestInterface $request) use (&$response) {
                /**
                 * @var \Phalconeer\CurlClient\Data\CurlResponse $response
                 */
                $response = $this->client->sendRequest($request);
                $response = $response->setRequestId($request->requestId());
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