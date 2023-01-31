<?php
namespace Phalconeer\Bootstrap;

use Phalconeer\Bootstrap;
use Phalcon\Http\ResponseInterface;

class BootstrapRest extends Bootstrap\Bootstrap
{
    /**
     * Runs the application.
     *
     * @return string   The HTTP response body.
     */
    protected function runApplication()
    {
        $router = $this->di->get('router');
        $dispatcher = $this->di->get('dispatcher');
        $response = $this->di->get('response');

        $router->handle();

        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());
        $dispatcher->dispatch();

        if ($response instanceof ResponseInterface) {
            $response->send();
        }
    }
}