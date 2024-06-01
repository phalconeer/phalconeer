<?php
namespace Phalconeer\Config\Trait;

use Phalconeer\Config as This;
use Phalcon;

trait Get
{
    public function getConfig() : Phalcon\Config\Config
    {
        return $this->di->get(This\Factory::MODULE_NAME);
    }
}