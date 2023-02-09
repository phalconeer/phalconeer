<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ArrayExporter
{
    use This\Traits\ConvertedValue;

    // protected bool $_convertChildren = true;

    // protected bool $_preserveKeys = false;

    /**
     * Returns an array representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArray() : array
    {
        $convertChildren = isset(static::$_convertChildren)
            ? static::$_convertChildren
            : true;

        $preserveKeys = isset(static::$_preserveKeys)
            ? static::$_preserveKeys
            : true;

        if ($this instanceof Data\CollectionInterface) {
            return $this->convertCollection($convertChildren, $preserveKeys)->getArrayCopy();
        }
        return array_reduce(
            $this->properties(),
            function (array $aggregator, $propertyName) use ($convertChildren, $preserveKeys)
            {
                if ($convertChildren) {
                    $aggregator[$propertyName] = $this->getConvertedValue($propertyName, $preserveKeys);
                } else {
                    $aggregator[$propertyName] = $this->getValue($propertyName);
                }
                return $aggregator;
            },
            []
        );
    }
}