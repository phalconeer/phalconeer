<?php
namespace Phalconeer\Cache;

use Phalconeer\Bootstrap;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'cache';
    

    protected function configure() {
        return 'loaded';
    }
}