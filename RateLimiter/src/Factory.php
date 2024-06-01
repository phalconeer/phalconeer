<?php
namespace Phalconeer\RateLimiter;

use Phalconeer\Bootstrap;
use Phalconeer\Condition;
use Phalconeer\Config;
use Phalconeer\Dao;
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
        return new This\Bo\RateLimiterBo(
            new This\Dao\AllowAllDao(),
            $di->get(Impression\Factory::MODULE_NAME),
            $di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig),
        );
    }
}