<?php
namespace Phalconeer\Router;

use Phalcon;
use Phalcon\Mvc;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Router as This;

/**
 * Initializes the router.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'router';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get('router', new Phalcon\Config\Config());
        $applicationConfig = $this->di->get(Config\Factory::MODULE_NAME)->get('application', new Phalcon\Config\Config());

        $router = new This\Bo\RouterBo(
            new Mvc\Router(false),
            $config,
            $applicationConfig
        );

        return $router->getRouter();
    }
}
