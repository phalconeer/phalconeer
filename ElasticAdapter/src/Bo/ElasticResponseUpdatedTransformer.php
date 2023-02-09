<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\ElasticAdapter as This;
use Phalconeer\Middleware;
use Phalconeer\Module\Middleware\Dto\TerminateMiddleware;

class ElasticResponseUpdatedTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(Psr\Http\Message\ResponseInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        if ($response->bodyVariableExists(This\Helper\ElasticResponseHelper::NODE_RESULT)
            && $response->bodyVariable(This\Helper\ElasticResponseHelper::NODE_RESULT) === This\Helper\ElasticResponseHelper::VALUE_UPDATED) {
            $next($response, new TerminateMiddleware());
            return null;
        }

        $next($response);
        return null;
    }
}