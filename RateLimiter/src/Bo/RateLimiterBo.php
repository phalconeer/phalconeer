<?php
namespace Phalconeer\RateLimiter\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Condition;
use Phalconeer\Impression;

class RateLimiterBo
{
    public function __construct(
        protected Impression\Bo\ImpressionBo $impression,
        protected PhalconConfig\Config $config
    )
    {
    }

    protected function generateHash(array $identifiers) : string
    {
        return 'rl-' . md5(json_encode($identifiers));
    }

    public function tag(array $identifiers) : string
    {
        $hash = $this->generateHash($identifiers);
        $this->impression->addTag($hash);
        return $hash;
    }

    public function check(
        array $identifiers,
        ?int $limit = null,
        ?int $interval = null //seconds
    ) : bool
    {
        if (is_null($limit)) {
            $limit = $this->config->get('limit', 5);
        }
        if (is_null($interval)) {
            $interval = $this->config->get('interval', 60);
        }
        $hash = $this->tag($identifiers);
        $impressionCount = $this->impression->getImpressionCount([
            'tags'          => $hash,
            'requestTime'   => [
                'operator'      => Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL,
                'value'         => new \DateTime('-' . $interval . 'seconds')
            ]
        ]);

        return $impressionCount < $limit;
    }
}