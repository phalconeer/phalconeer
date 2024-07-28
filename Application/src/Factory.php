<?php
namespace Phalconeer\Application;

use Phalconeer\Application as This;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalcon;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'application';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        $defaultConfig = $this->di->get(Config\Factory::MODULE_NAME)?->application;
        return function (Phalcon\Config\Config $config = null) use ($defaultConfig) {
            $bo = new This\Bo\ApplicationBo(
                $config ?? $defaultConfig
            );
            return $bo;
        };
    }
}