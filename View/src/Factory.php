<?php
namespace Phalconeer\View;

use Phalcon\Mvc\View;
use Phalconeer\Bootstrap;
use Phalconeer\Config;

/**
 * Initializes the view.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'view';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/view_config.php'
    ];

    protected function configure() {
        $view = new View();
        $view->setViewsDir($this->di->get(Config\Factory::MODULE_NAME)->view->viewsDir);
        if ($this->di->get(Config\Factory::MODULE_NAME)->view->has('engines')) {
            $view->registerEngines($this->di->get(Config\Factory::MODULE_NAME)->view->engines->toArray());
        }
        return $view;
    }
}