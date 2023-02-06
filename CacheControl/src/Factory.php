<?php
namespace Phalconeer\CacheControl;

use Phalconeer\Bootstrap;
use Phalconeer\Cache;
use Phalconeer\CacheControl as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'cacheControl';
    
    protected static array $requiredModules = [
        'request',
        Cache\Factory::MODULE_NAME,
    ];

    protected function configure() {
        return new This\Bo\CacheControlBo($this->di->get('request'));
    }
}