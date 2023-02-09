<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Dto as This;

trait ArrayObjectExporter
{
    use This\Traits\ConvertedValue,
        This\Traits\ArrayExporter;

    /**
     * Returns an arrayObject representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArrayObject(
        bool $convertChildren = null,
        bool $preserveKeys = null
    ) : \ArrayObject
    {
        if (is_null($convertChildren)) {
            $convertChildren = $this->_convertChildren;
        }
        if (is_null($preserveKeys)) {
            $preserveKeys = $this->_preserveKeys;
        }
        
        return new \ArrayObject($this->toArray($convertChildren, $preserveKeys));
    }
}