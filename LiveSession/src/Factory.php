<?php
namespace Phalconeer\LiveSession;

use Phalconeer\Application;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\LiveSession as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'liveSession';
    
    protected static array $requiredModules = [
        Application\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/session_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(
            static::MODULE_NAME,
            Config\Helper\ConfigHelper::$dummyConfig
        );

        return new This\Bo\LiveSessionBo(
            new This\Bo\DummyAdapter(),
            $this->di->get(Application\Factory::MODULE_NAME),
            $config
        );
    }
}