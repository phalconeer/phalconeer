<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message\ResponseInterface;
use Phalconeer\Module\Browser\ResponseMiddlewareInterface;
use Phalconeer\Module\Http\Helper\MessageHelper;
use Phalconeer\Module\Middleware\DefaultMiddleware;

class ElasticResponseTransformer extends DefaultMiddleware implements ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    public function handleResponse(ResponseInterface $response, callable $next) : ?bool
    {
        // This transfromation is needed for all the deafult ElasticTransformers
        $response = $response->withBodyVariables(json_decode($response->bodyVariables()[MessageHelper::FULL_TEXT_BODY], 1));

// echo \Phalconeer\Helper\TVarDumper::dump($response->bodyVariables());

        $next($response);
        return null;
    }
}