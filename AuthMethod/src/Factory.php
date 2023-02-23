<?php
namespace Phalconeer\AuthMethod;

use Phalconeer\Bootstrap;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authMethod';

    protected function configure() {
        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}