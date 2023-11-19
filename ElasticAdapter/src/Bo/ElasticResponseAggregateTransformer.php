<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr;
use Phalconeer\Browser;
use Phalconeer\ElasticAdapter\Helper\ElasticResponseHelper as ERH;
use Phalconeer\Http;
use Phalconeer\Middleware;

class ElasticResponseAggregateTransformer extends Middleware\Bo\DefaultMiddleware implements Browser\ResponseMiddlewareInterface
{
    protected static $handlerName = 'handleResponse';

    /**
     * There are three cases considered here
     * - #1 ONLY `key` and `doc_count` keys exist, meaning that this level does not have any nested aggregations, no information is needed other than `doc_count`
     * - #2 key `buckets` exist, this is a higher level of aggregation, meaning the data is in a nested level
     * - #3 there are aggregations with custom labels which needs to be crawled
     */
    protected function handleAggregate(array $aggregate)
    {
        $dataKeys = array_diff(array_keys($aggregate), ERH::NODE_NOT_AGGREGATE_DEFINITIONS);
        if (empty($dataKeys)) {
            // #1
            return $aggregate[ERH::NODE_DOC_COUNT];
        }

        if (array_key_exists(ERH::NODE_BUCKETS, $aggregate)) {
            // #2
            return array_reduce(
                $aggregate[ERH::NODE_BUCKETS],
                function ($result, $currentBucket) {
                    $result[$currentBucket[ERH::NODE_KEY]] = $this->handleAggregate($currentBucket);
                    return $result;
                },
                []
            );
        }

        // #3
        return array_reduce(
            array_keys($aggregate),
            function ($result, $key) use ($aggregate) {
                if (in_array($key, ERH::NODE_NOT_AGGREGATE_DEFINITIONS)) {
                    return $result;
                }

                $result[$key] = $aggregate[$key];
                return $result;
            },
            []
        );
    }

    public function handleResponse(Psr\Http\Message\ResponseInterface | Http\MessageInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        $result = [];
        if ($response->bodyVariableExists(ERH::NODE_AGGS)) {
            $aggregates = $response->bodyVariable(ERH::NODE_AGGS);
            $result = array_reduce(
                array_keys($aggregates),
                function ($result, $key) use ($aggregates) {
                    $result[$key] = $this->handleAggregate($aggregates[$key]);
                    return $result;
                },
                []
            );

            $response = $response->withBodyVariables($result);
            $next($response, new Middleware\Data\TerminateMiddleware());
            return null;
        }

        $next($response);
        return null;
    }
}