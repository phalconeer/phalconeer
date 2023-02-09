<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message;
use Phalconeer\Module\Browser;
use Phalconeer\Module\ElasticAdapter as This;
use Phalconeer\Module\Middleware;

class ElasticResponseErrorTransformer extends Middleware\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(Message\ResponseInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        if ($response->bodyVariableExists(This\Helper\ElasticResponseHelper::NODE_ERROR)) {
            This\Helper\ExceptionHelper::handleException($response->bodyVariable(This\Helper\ElasticResponseHelper::NODE_ERROR));
        }

        $next($response);
        return null;
    }
}