<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ArrayObjectNotNullExporter
{
    use This\Trait\ConvertedValue;

    /**
     * Returns an arrayObject representation of the object.
     * If convertChildren is false, a clone of the each complex nested object is returned
     * If copyNullsAsDefaults is false, null values are not exported
     *
     */
    public function toArrayObjectWithoutNulls() : \ArrayObject
    {
        if ($this instanceof Data\CollectionInterface) {
            return $this->convertCollection(
                $this->getConvertChildren(),
                $this->getPreserveKeys()
            );
        }
        $result = new \ArrayObject();
        array_map(
            function ($propertyName) use ($result)
            {
                if (!isset($this->{$propertyName})
                    || is_null($this->{$propertyName})) {
                    return;
                }
                if ($this->getConvertChildren()) {
                    $result->offsetSet(
                        $propertyName,
                        $this->getConvertedValue($propertyName, $this->getPreserveKeys())
                    );
                } else {
                    $result->offsetSet(
                        $propertyName,
                        $this->getValue($propertyName)
                    );
                }
            },
            $this->properties()
        );
        return $result;
    }
}