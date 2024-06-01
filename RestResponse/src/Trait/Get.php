<?php
namespace Phalconeer\RestResponse\Trait;

use Phalconeer\RestResponse as This;

trait Get
{
    public function getResponse() : This\Bo\RestResponse
    {
        return $this->di->get(This\Factory::MODULE_NAME);
    }
}