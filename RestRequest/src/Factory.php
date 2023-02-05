<?php
namespace Phalconeer\RestRequest;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\RestRequest as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'request';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/rest_request_config.php'
    ];

    protected function configure()
    {
        $filter = $this->di->get('filter');
        $config = $this->di->get(Config\Factory::MODULE_NAME)->restRequest;
        if ($this->di->get(Config\Factory::MODULE_NAME)->application->has('request')) {
            $config = $config->merge($this->di->get(Config\Factory::MODULE_NAME)->application->request);
        }
        return new This\Bo\RestRequest(
            $filter,
            $config
        );
    }
}
