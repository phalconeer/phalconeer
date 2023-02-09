<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message\ResponseInterface;
use Phalconeer\Module\Browser\ResponseMiddlewareInterface;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticResponseHelper;
use Phalconeer\Module\Middleware\DefaultMiddleware;

class ElasticResponseSimpleResultTransformer extends DefaultMiddleware implements ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    protected function handleSimpleresult(ResponseInterface $response)
    {
        [
            ElasticResponseHelper::NODE_HITS_TOTAL => $total,
            ElasticResponseHelper::NODE_HITS_MAX_SCORE => $maxScore,
            ElasticResponseHelper::NODE_HITS => $list
        ] = $response->bodyVariable(ElasticResponseHelper::NODE_HITS);

        return $response
            ->withBodyVariables([
                ElasticResponseHelper::NODE_HITS_TOTAL        => $total[ElasticResponseHelper::NODE_VALUE],
                ElasticResponseHelper::NODE_HITS_MAX_SCORE    => $maxScore,
                ElasticResponseHelper::NODE_HITS              => array_map(
                    function ($currentElement) {
                        $return = array_merge($currentElement[ElasticResponseHelper::NODE_HITS_SOURCE], $currentElement);
                        unset($return[ElasticResponseHelper::NODE_HITS_SOURCE]);
                        return $return;
                    },
                    $list
                )
            ]);
    }

    public function handleResponse(ResponseInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        if ($response->bodyVariableExists(ElasticResponseHelper::NODE_HITS)) {
            $response = $this->handleSimpleResult($response);
        }

        $next($response);
        return null;
    }
}