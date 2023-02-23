<?php
namespace Phalconeer\Condition;

use Phalconeer\Bootstrap;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'condition';

    protected function configure()
    {
        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}