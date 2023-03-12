<?php
namespace Phalconeer\ExceptionForwardToController;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ExceptionForwardToController as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'exceptionForwardToController';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request'
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/config_dispatcher.php'
    ];

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)
            ->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        $exceptionDescriptors = $this->di->get(Config\Factory::MODULE_NAME)
            ->get('exceptionDescriptors', Config\Helper\ConfigHelper::$dummyConfig);

        return new This\Bo\ExceptionForwardToControllerBo(
            $config,
            $exceptionDescriptors
        );
    }
}