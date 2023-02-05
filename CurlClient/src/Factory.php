<?php
namespace Phalconeer\CurlClient;

use Phalconeer\Bootstrap;
use Phalconeer\CurlClient as This;
use Phalconeer\Http;
use Phalconeer\Loader;

/**
 *
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'curlClient';
    
    protected static array $requiredModules = [
        Http\Factory::MODULE_NAME,
        Loader\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        return function (This\Data\CurlOptions $options = null) {
            return new This\Bo\CurlClientBo($options);
        };
    }
}