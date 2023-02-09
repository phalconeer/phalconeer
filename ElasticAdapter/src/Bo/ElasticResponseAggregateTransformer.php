<?php
namespace Phalconeer\ElasticAdapter\Bo;

use Psr\Http\Message\ResponseInterface;
use Phalconeer\Module\Browser\ResponseMiddlewareInterface;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticResponseHelper;
use Phalconeer\Module\Middleware\DefaultMiddleware;
use Phalconeer\Module\Middleware\Dto\TerminateMiddleware;

class ElasticResponseAggregateTransformer extends DefaultMiddleware implements ResponseMiddlewareInterface
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
        $dataKeys = array_diff(array_keys($aggregate), ElasticResponseHelper::NODE_NOT_AGGREGATE_DEFINITIONS);
        if (empty($dataKeys)) {
            // #1
            return $aggregate[ElasticResponseHelper::NODE_DOC_COUNT];
        }

        if (array_key_exists(ElasticResponseHelper::NODE_BUCKETS, $aggregate)) {
            // #2
            return array_reduce(
                $aggregate[ElasticResponseHelper::NODE_BUCKETS],
                function ($result, $currentBucket) {
                    $result[$currentBucket[ElasticResponseHelper::NODE_KEY]] = $this->handleAggregate($currentBucket);
                    return $result;
                },
                []
            );
        }

        // #3
        return array_reduce(
            array_keys($aggregate),
            function ($result, $key) use ($aggregate) {
                if (in_array($key, ElasticResponseHelper::NODE_NOT_AGGREGATE_DEFINITIONS)) {
                    return $result;
                }

                $result[$key] = $aggregate[$key];
                return $result;
            },
            []
        );
    }

    public function handleResponse(ResponseInterface $response, callable $next) : ?bool
    {
        // This assumes that the response has already been json_decoded
        $result = [];
        if ($response->bodyVariableExists(ElasticResponseHelper::NODE_AGGS)) {
            $aggregates = $response->bodyVariable(ElasticResponseHelper::NODE_AGGS);
            $result = array_reduce(
                array_keys($aggregates),
                function ($result, $key) use ($aggregates) {
                    $result[$key] = $this->handleAggregate($aggregates[$key]);
                    return $result;
                },
                []
            );

            $response = $response->withBodyVariables($result);
            $next($response, new TerminateMiddleware());
            return null;
        }

        $next($response);
        return null;
    }
}