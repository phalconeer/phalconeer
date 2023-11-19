<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\ElasticAdapter\Helper\ElasticResponseHelper as ERH;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticResponseSimpleResultTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    protected function handleSimpleresult(Psr\Http\Message\ResponseInterface $response)
    {
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        [
            ERH::NODE_HITS_TOTAL => $total,
            ERH::NODE_HITS_MAX_SCORE => $maxScore,
            ERH::NODE_HITS => $list
        ] = $response->bodyVariable(ERH::NODE_HITS);

        return $response
            ->withBodyVariables([
                ERH::NODE_HITS_TOTAL        => $total[ERH::NODE_VALUE],
                ERH::NODE_HITS_MAX_SCORE    => $maxScore,
                ERH::NODE_HITS              => array_map(
                    function ($currentElement) {
                        $return = array_merge($currentElement[ERH::NODE_HITS_SOURCE], $currentElement);
                        unset($return[ERH::NODE_HITS_SOURCE]);
                        return $return;
                    },
                    $list
                )
            ]);
    }

    public function handleResponse(Psr\Http\Message\ResponseInterface | Http\MessageInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        if ($response->bodyVariableExists(ERH::NODE_HITS)) {
            $response = $this->handleSimpleResult($response);
        }

        $next($response);
        return null;
    }
}