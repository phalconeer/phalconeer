<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\ElasticAdapter as This;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticResponseErrorTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(Psr\Http\Message\ResponseInterface | Http\MessageInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        if ($response->bodyVariableExists(This\Helper\ElasticResponseHelper::NODE_ERROR)) {
            This\Helper\ExceptionHelper::handleException($response->bodyVariable(This\Helper\ElasticResponseHelper::NODE_ERROR));
        }

        $next($response);
        return null;
    }
}