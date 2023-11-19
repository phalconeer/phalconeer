<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\ElasticAdapter\Helper\ElasticResponseHelper as ERH;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticResponseCreatedTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(Psr\Http\Message\ResponseInterface | Http\MessageInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        if ($response->bodyVariableExists(ERH::NODE_RESULT)
            && $response->bodyVariable(ERH::NODE_RESULT) === ERH::VALUE_CREATED) {
            $next($response, new Middleware\Data\TerminateMiddleware());
            return null;
        }

        $next($response);
        return null;
    }
}