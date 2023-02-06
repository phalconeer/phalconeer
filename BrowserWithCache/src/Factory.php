<?php
namespace Phalconeer\BrowserWithCache;

use Phalcon;
use Phalconeer\Cache;
use Phalconeer\Config;
use Phalconeer\Bootstrap;
use Phalconeer\Loader;
use Phalconeer\Middleware;
use Phalconeer\Browser;
use Phalconeer\BrowserWithCache as This;
use Phalconeer\CurlClient;

/**
 * Required composer modules
 * "nyholm/psr7":          "^1.0"
 */
class Factpry extends Bootstrap\Factory
{
    const MODULE_NAME = 'browserWithCache';
    
    protected static $requiredModules = [
        Browser\Factory::MODULE_NAME,
        Cache\Factory::MODULE_NAME,
        CurlClient\Factory::MODULE_NAME,
        Loader\Factory::MODULE_NAME,
        Middleware\Factory::MODULE_NAME,
    ];

    /**
     * Configures the Bootstrap module
     */
    protected function configure() {
        $di = $this->di;
        return function (
            CurlClient\CurlClientInterface $client,
            Cache\CacheAdapterInterface $cache,
            Cache\Data\CacheSettings $cacheSettings,
            array $requestMiddlewares = [],
            array $responseMiddlewares = [],
            Phalcon\Config\Config $config = null
        ) use ($di) : This\Bo\BrowserWithCacheBo
       {
            $moduleConfig = $di->get(Config\Factory::MODULE_NAME)->get(
                'browserWitchCache',
                new Phalcon\Config\Config()
            );
            if (!is_null($config)) {
                $moduleConfig->merge($config);
            }
            return new This\Bo\BrowserWithCacheBo(
                $client,
                $requestMiddlewares,
                $responseMiddlewares,
                $cache,
                $cacheSettings,
                $moduleConfig
            );
        };
    }
}