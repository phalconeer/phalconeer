<?php
namespace Phalconeer\Loader\Bo;

use Phalcon\Autoload;
use Phalcon\Config;
use Phalconeer\Loader as This;

class LoaderBo
{
    protected Autoload\Loader $loader;

    protected Config\Config $config;

    public function __construct(
        Autoload\Loader $loader,
        Config\Config $config = null
    )
    {
        $this->loader = $loader;
        $this->config = $config;

        if (is_null($this->config)) {
             throw new This\Exception\InvalidLoaderConfigurationException(
                'Invalid loader configuration. Please set the application key in global config',
                This\Helper\ExceptionHelper::LOADER__MISSING_CONFIGURATION
             );
        }

        if ($this->config->has('directories')){
            $this->loadDirectories($this->config->get('directories'));
        }
        if ($this->config->has('namespaces')){
            $this->loadNamespaces($this->config->get('namespaces'));
        }

        $this->loader->register(true);
    }

    public function loadDirectories(Config\Config $directories, $merge = false)
    {
        $this->loader->setDirectories($directories->toArray(), $merge);
    }

    public function loadNamespaces(Config\Config $namespaces, $merge = false)
    {
        $this->loader->setNamespaces($namespaces->toArray(), $merge);
    }
}
