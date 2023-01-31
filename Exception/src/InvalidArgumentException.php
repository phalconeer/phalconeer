<?php

namespace Phalconeer\Exception;

use Phalconeer\Exception as This;

class InvalidArgumentException extends \InvalidArgumentException implements This\ExceptionInterface
{
    protected $component = 'unknown';

    public function getComponent() : string
    {
        return $this->component;
    }
}
