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
        if ($this instanceof Data\CollectionInterface) {
            return $this->convertCollection(
                $this->getConvertChildren(),
                $this->getPreserveKeys()
            )->getArrayCopy();
        }
        return array_reduce(
            $this->properties(),
            function (array $aggregator, $propertyName)
            {
                if ($this->getConvertChildren()) {
                    $aggregator[$propertyName] = $this->getConvertedValue($propertyName, $this->getPreserveKeys());
                } else {
                    $aggregator[$propertyName] = $this->getValue($propertyName);
                }
                return $aggregator;
            },
            []
        );
    }
}