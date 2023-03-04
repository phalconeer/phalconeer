<?php
namespace Phalconeer\RateLimiter;

use Phalconeer\Bootstrap;
use Phalconeer\Condition;
use Phalconeer\Config;
use Phalconeer\Impression;
use Phalconeer\RateLimiter as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'rateLimiter';
    
    protected static array $requiredModules = [
        Condition\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
        Impression\Factory::MODULE_NAME,
    ];

    protected function configure() {
        $di = $this->di;
        $moduleName = static::MODULE_NAME;
        return function (Impression\Bo\ImpressionBo $impressionBo = null) use ($di, $moduleName){
            if (is_null($impressionBo)) {
                $impressionBo = $di->get(Impression\Factory::MODULE_NAME);
            }
            return new This\Bo\RateLimiterBo(
                $impressionBo,
                $di->get(Config\Factory::MODULE_NAME)->get($moduleName, Config\Helper\ConfigHelper::$dummyConfig),
            );
        };
    }
}