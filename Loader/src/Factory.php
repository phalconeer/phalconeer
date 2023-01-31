<?php
namespace Phalconeer\Loader;

// require_once __DIR__ . '/Bo/LoaderBo.php';
// require_once __DIR__ . '/Exception/InvalidLoaderConfigurationException.php';

use Phalcon\Autoload;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Loader as This;

/**
 * Initializes the loader.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'loader';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        return new This\Bo\LoaderBo(
            new Autoload\Loader,
            $this->di->get(Config\Factory::MODULE_NAME)->get('application')
        );
    }
}
