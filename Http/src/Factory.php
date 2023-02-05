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

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get('http');
        if (!is_null($config)
            && $config->has('namespaces')) {
            $this->di->get(Loader\Factory::MODULE_NAME)->loadNamespaces(
                $config->namespaces,
                true
            );
        }

        return 'loaded';
    }
}