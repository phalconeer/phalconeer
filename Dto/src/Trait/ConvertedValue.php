<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ConvertedValue
{
    public function convertCollection(bool $preserveKeys = false) : \ArrayObject
    {
        $iterator = $this->getIterator();
        $result = new \ArrayObject();
        while ($iterator->valid()) {
            $result->offsetSet(
                ($preserveKeys) ? $iterator->key() : null,
                $iterator->current()->export()
            );
            $iterator->next();
        }
        return $result;
    }

    public function convertDataInterface($propertyName)
    {
        if (!method_exists($this->{$propertyName}, 'export')) {
            throw new This\Exception\ExportFunctionMissingException(
                get_class($this->{$propertyName}) . ' called in ' . get_class($this),
                This\Helper\ExceptionHelper::DTO__MISSING_EXPORT_FUNCTION
            );
        }
        return $this->{$propertyName}->export();
    }

    public function getConvertedValue(
        $propertyName,
        $preserveKeys = false
    )
    {
        if (!isset($this->{$propertyName}) // Added as with typed properties, the object can be in uninitialzed state, which throws property "must not be accessed before initialization"
            || is_null($this->{$propertyName})
            || !$this->meta->doesPropertyExist($propertyName)) {
            return null;
        }

        $exportFunction = 'export' . ucfirst($propertyName);
        if (method_exists($this, $exportFunction)) {
            return call_user_func([$this, $exportFunction]);
        }

       $propertyType = $this->meta->propertyType($propertyName);

        if (Data\Helper\ParseValueHelper::isSimpleValue($propertyType)
            || ($propertyType === Data\Property\Any::class
                && !is_object($this->{$propertyName}))) {
            return $this->{$propertyName};
        } else {
            switch (true) {
                case $this->{$propertyName} instanceof Data\DataInterface:
                    return $this->convertDataInterface($propertyName, $preserveKeys);
                case is_callable([$this->{$propertyName}, 'getArrayCopy']):
                    return $this->{$propertyName}->getArrayCopy();
                case $this->{$propertyName} instanceof \DateTime:
                    return $this->{$propertyName}->format('c');
                default:
                    return json_encode($this->{$propertyName});
            }
        }
    }
}