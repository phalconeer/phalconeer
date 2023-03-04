<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticResponseTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(Psr\Http\Message\ResponseInterface $response, callable $next) : ?bool
    {
// echo \Phalconeer\Dev\TVarDumper::dump($response);
        // This transfromation is needed for all the deafult ElasticTransformers
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        $response = $response->withBodyVariables(json_decode($response->bodyVariables()[Http\Helper\MessageHelper::FULL_TEXT_BODY], 1));

        $next($response);
        return null;
    }
}