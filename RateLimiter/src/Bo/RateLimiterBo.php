<?php
namespace Phalconeer\RateLimiter\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Condition;
use Phalconeer\Dao;
use Phalconeer\Impression;

class RateLimiterBo
{
    public function __construct(
        protected Dao\DaoReadInterface $adapter,
        protected Impression\ImpressionBoInterface $impression,
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
        $limit = $limit ?? $this->config->get('limit', 5);
        $interval = $interval ?? $this->config->get('interval', 60);
        
        $hash = $this->tag($identifiers);
        $impressionCount = $this->adapter->getCount([
            'tags'          => $hash,
            'requestTime'   => [
                'operator'      => Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL,
                'value'         => new \DateTime('-' . $interval . 'seconds')
            ]
        ]);

        return $impressionCount < $limit;
    }
}