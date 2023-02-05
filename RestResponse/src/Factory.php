<?php
namespace Phalconeer\RestResponse;

use Phalcon\Events;
use Phalcon\Mvc\Application;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\RestRequest;
use Phalconeer\RestResponse as This;

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

    protected function configure()
    {
        $responseConfig = $this->di->get(Config\Factory::MODULE_NAME)->response;

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
        if ($this->di->get(Config\Factory::MODULE_NAME)->application->has('name')) {
            $response = $response->setApplicationName($this->di->get(Config\Factory::MODULE_NAME)->application->get('name'));
        }

        if ($responseConfig 
            && $responseConfig->offsetExists('eventListeners')) {
            $eventsManager = new Events\Manager();
            foreach ($responseConfig->eventListeners as $event => $listener) {
                foreach ($listener->toArray() as $currentListener) {
                    $eventsManager->attach($event, new $currentListener);
                }
            }
            $response->setEventsManager($eventsManager);
        }

        return $response;
    }
}
