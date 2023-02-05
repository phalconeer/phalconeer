<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait ConvertedValue
{
    public function convertCollection(
        bool $convertChildren = true,
        bool $preserveKeys = false
    )
    {
        $iterator = $this->getIterator();
        $result = [];
        while ($iterator->valid()) {
            if ($preserveKeys) {
                $result[$iterator->key()] = $iterator->current()->export($convertChildren, $preserveKeys);
            } else {
                $result[] = $iterator->current()->export($convertChildren, $preserveKeys);
            }
            $iterator->next();
        }
        return $result;
    }

    public function convertDataInterface(
        $propertyName,
        $preserveKeys = false
    )
    {
        if (!method_exists($this->{$propertyName}, 'export')) {
            throw new This\Exception\ExportFunctionMissingException(
                get_class($this->{$propertyName}) . ' called in ' . get_class($this),
                This\Helper\ExceptionHelper::DTO__MISSING_EXPORT_FUNCTION
            );
        }
        return $this->{$propertyName}->export(
            true,
            $preserveKeys
        );
    }

    public function getConvertedValue(
        $propertyName,
        $preserveKeys = false
    )
    {
        if (!isset($this->{$propertyName}) // Added as with typed properties, the object can be in uninitialzed state, which throws property "must not be accessed before initialization"
            || is_null($this->{$propertyName})
            || !array_key_exists($propertyName, $this->_propertiesCache)) {
            return null;
        }

        $exportFunction = 'export' . ucfirst($propertyName);
        if (method_exists($this, $exportFunction)) {
            return call_user_func([$this, $exportFunction]);
        }

        if (Data\Helper\ParseValueHelper::isSimpleValue($this->_propertiesCache[$propertyName])
            || ($this->_propertiesCache[$propertyName] === Data\Property\Any::class
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