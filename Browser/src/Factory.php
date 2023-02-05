<?php
namespace Phalconeer\Module\Browser;

use Phalconeer\Bootstrap;
use Phalconeer\Loader;
use Phalconeer\Middleware;
use Phalconeer\Browser as This;
use Phalconeer\CurlClient;

/**
 * Required composer modules
 * "nyholm/psr7":          "^1.0"
 */
class Factpry extends Bootstrap\Factory
{
    const MODULE_NAME = 'browser';
    
    protected static $requiredModules = [
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
            array $requestMiddlewares = [],
            array $responseMiddlewares = []
        ) use ($di) : This\Bo\BrowserBo
       {
            return new This\Bo\BrowserBo(
                $client,
                $di->get(Middleware\Factory::MODULE_NAME),
                $requestMiddlewares,
                $responseMiddlewares
            );
        };
    }
}