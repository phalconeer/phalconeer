<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ArrayExporter
{
    use This\Traits\ArrayObjectExporter;

    /**
     * Returns an array representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArray() : array
    {
        return $this->toArrayObject()->getArrayCopy();
    }
}