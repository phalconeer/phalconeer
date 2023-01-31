<?php
namespace Phalconeer\Exception;

use Phalconeer\Exception as This;

class Exception extends \Exception implements This\ExceptionInterface
{
    protected string $component = 'unknown';

    public function getComponent() : string
    {
        return $this->component;
    }
}
