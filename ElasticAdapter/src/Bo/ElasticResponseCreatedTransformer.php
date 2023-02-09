<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message\ResponseInterface;
use Phalconeer\Module\Browser\ResponseMiddlewareInterface;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticResponseHelper;
use Phalconeer\Module\Middleware\DefaultMiddleware;
use Phalconeer\Module\Middleware\Dto\TerminateMiddleware;

class ElasticResponseCreatedTransformer extends DefaultMiddleware implements ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(ResponseInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        if ($response->bodyVariableExists(ElasticResponseHelper::NODE_RESULT)
            && $response->bodyVariable(ElasticResponseHelper::NODE_RESULT) === ElasticResponseHelper::VALUE_CREATED) {
            $next($response, new TerminateMiddleware());
            return null;
        }

        $next($response);
        return null;
    }
}