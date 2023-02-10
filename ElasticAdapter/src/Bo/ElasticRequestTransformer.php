<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticRequestTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\RequestMiddlewareInterface
{
    protected static $handlerName = 'handleRequest';

    public function handleRequest(Psr\Http\Message\RequestInterface $request, callable $next) : ?bool
    {
        /**
         * @var \Phalconeer\Http\Data\Request $request
         */
        $body = array_key_exists(Http\Helper\MessageHelper::FULL_TEXT_BODY, $request->bodyVariables())
            ? $request->bodyVariables()[Http\Helper\MessageHelper::FULL_TEXT_BODY]
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
                    Http\Helper\MessageHelper::FULL_TEXT_BODY   => $body
                ]
            );
// echo \Phalconeer\Helper\TVarDumper::dump($body) . PHP_EOL;
// echo \Phalconeer\Helper\TVarDumper::dump($request);
// die();
        $next($request);
        return null;
    }
}