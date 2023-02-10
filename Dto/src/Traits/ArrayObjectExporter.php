<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Dto as This;

trait ArrayObjectExporter
{
    use This\Traits\ArrayExporter;

    /**
     * Returns an arrayObject representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArrayObject() : \ArrayObject
    {
        return new \ArrayObject($this->toArray());
    }
}