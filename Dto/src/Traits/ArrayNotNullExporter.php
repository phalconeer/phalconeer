<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ArrayNotNullExporter
{
    use This\Traits\ConvertedValue;

    /**
     * Returns an array representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArrayWithoutNulls() : array
    {
        return array_reduce(
            $this->properties(),
            function (array $aggregator, $propertyName)
            {
                if (!isset($this->{$propertyName})
                    || is_null($this->{$propertyName})) {
                    return $aggregator;
                }
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