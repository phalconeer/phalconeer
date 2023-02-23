<?php
namespace Phalconeer\Http;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Loader;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'http';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Loader\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/http_config.php',
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME);
        if (!is_null($config)
            && $config->has('namespaces')) {
            $this->di->get(Loader\Factory::MODULE_NAME)->loadNamespaces(
                $config->namespaces,
                true
            );
        }

        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}