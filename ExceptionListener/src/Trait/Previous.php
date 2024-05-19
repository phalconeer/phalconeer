<?php
namespace Phalconeer\ExceptionListener\Trait;
use Phalconeer\ExceptionListener as This;

trait Previous {

    protected ?This\Data\Exception $previous;

    public function setPrevious(This\Data\Exception $previous) : self
    {
        return $this->setValueByKey('previous', $previous);
    }
}