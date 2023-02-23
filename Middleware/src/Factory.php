<?php
namespace Phalconeer\Middleware;

use Phalconeer\Bootstrap;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'middleware';
    
    protected function configure()
    {
        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}