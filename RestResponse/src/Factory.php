<?php
namespace Phalconeer\RestResponse;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\RestRequest;
use Phalconeer\RestResponse as This;
use Phalcon;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'response';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        RestRequest\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/rest_response_config.php'
    ];

    protected function setApplicationName(This\Bo\RestResponse $response) : This\Bo\RestResponse
    {
        if ($this->di->get(Config\Factory::MODULE_NAME)->application->has('name')) {
            $response = $response->setApplicationName($this->di->get(Config\Factory::MODULE_NAME)->application->get('name'));
        }
        return $response;
    }

    protected function attachEventListeners(Phalcon\Http\Response $response) : Phalcon\Http\ResponseInterface
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->restResponse;

        if ($config 
            && $config->has('eventListeners')) {
            $eventsManager = new Phalcon\Events\Manager();
            foreach ($config->eventListeners as $listener) {
                $eventsManager->attach('response', $this->di->get($listener));
            }

            $response->setEventsManager($eventsManager);
        }

        return $response;
    }

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->restResponse;
        if ($this->di->get(Config\Factory::MODULE_NAME)->application->has('request')) {
            $config = $config->merge($this->di->get(Config\Factory::MODULE_NAME)->application->request);
        }
        $response = new This\Bo\RestResponse(
            null,
            null,
            null,
            $this->di->get(RestRequest\Factory::MODULE_NAME),
            $this->di->get('url'),
            $config
        );

        $response = $this->setApplicationName($response);
        $response = $this->attachEventListeners($response);

        return $response;
    }
}
