<?php
namespace Phalconeer\Application;

use Phalconeer\Application as This;
use Phalconeer\Bootstrap;
use Phalconeer\Config;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'application';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        $bo = new This\Bo\ApplicationBo(
            $this->di->get(Config\Factory::MODULE_NAME)->application
        );
        return $bo;
    }
}