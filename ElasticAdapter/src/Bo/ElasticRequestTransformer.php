<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message\RequestInterface;
use Phalconeer\Module\Browser\RequestMiddlewareInterface;
use Phalconeer\Module\Http\Helper\MessageHelper;
use Phalconeer\Module\Middleware\DefaultMiddleware;

class ElasticRequestTransformer extends DefaultMiddleware implements RequestMiddlewareInterface
{
    protected static $handlerName = 'handleRequest';

    public function handleRequest(RequestInterface $request, callable $next) : ?bool
    {
        $body = array_key_exists(MessageHelper::FULL_TEXT_BODY, $request->bodyVariables())
            ? $request->bodyVariables()[MessageHelper::FULL_TEXT_BODY]
            : json_encode($request->bodyVariables());

        $request = $request
            ->withHeaderVariables(
                [
                    'Content-Type'      => 'application/json'
                ],
                true
            )
            ->withBodyVariables(
                [
                    MessageHelper::FULL_TEXT_BODY   => $body
                ]
            );
// echo \Phalconeer\Helper\TVarDumper::dump($body) . PHP_EOL;
// echo \Phalconeer\Helper\TVarDumper::dump($request);
// die();
        $next($request);
        return null;
    }
}