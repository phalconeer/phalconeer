<?php
namespace Phalconeer\Impression;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Impression as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'impression';
    
    protected static $instances = [];

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request',
    ];

    protected function configure()
    {
        $request = $this->di->get('request');

        return function ($impression = null) use ($request) {
            return new This\Bo\ImpressionBo(
                new This\Dao\DummyDao(),
                $request,
                $impression
            );
        };
    }
}
