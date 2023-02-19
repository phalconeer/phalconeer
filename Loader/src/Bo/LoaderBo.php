<?php
namespace Phalconeer\Loader\Bo;

use Phalcon\Autoload;
use Phalcon\Config as PhalconConfig;
use Phalconeer\Loader as This;

class LoaderBo
{
    protected Autoload\Loader $loader;

    protected PhalconConfig\Config $config;

    public function __construct(
        Autoload\Loader $loader,
        PhalconConfig\Config $config = null
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

    public function loadDirectories(PhalconConfig\Config $directories, $merge = false)
    {
        $this->loader->setDirectories($directories->toArray(), $merge);
    }

    public function loadNamespaces(PhalconConfig\Config $namespaces, $merge = false)
    {
        $this->loader->setNamespaces($namespaces->toArray(), $merge);
    }

    public function addNamespace(
        string $name,
        $directories,
        bool $prepend = false
    )
    {
        $this->loader = $this->loader->addNamespace(
            $name,
            $directories,
            $prepend
        );
    }
}
