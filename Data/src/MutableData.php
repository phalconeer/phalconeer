<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;
use Phalconeer\Exception;

abstract class MutableData extends This\ImmutableData
{
    /**
     * Formats the value to the desired format and makes sure that nested objects are immutable.
     * Use transformer trais for generic cases (export functions has to be created in the object)
     */
    protected function getValue($propertyName)
    {
        if (!isset($this->{$propertyName}) // Added as with typed properties, the object can be in uninitialzed state, which throws property "must not be accessed before initialization"
            || is_null($this->{$propertyName})
            || !array_key_exists($propertyName, $this->_propertiesCache)) {
            return null;
        }

        return $this->{$propertyName};
    }

    /**
     * Takes in an object with new values and merges them with the existing data..
     * The object internals are not updated, a new instance is returned.
     */
    public function merge(This\DataInterface $changes) : This\DataInterface
    {
        foreach ($changes->properties() as $propertyName) {
            if (isset($changes->{$propertyName})
                && !is_null($changes->{$propertyName})) {
                $this->setValueByKey(
                    $propertyName,
                    $changes->{$propertyName}
                );
            }
        }

        return $this;
    }

    /**
     * Updates a field and return a new instance of the object
     */
    public function setValueByKey(
        string $key,
        $value,
        $isSilent = false
    ) : self
    {
        $propertyType = $this->propertyType($key);
        if (is_null($propertyType)) {
            throw new Exception\InvalidArgumentException(
                'Object property does not exist: ' . $key . ' @ ' . static::class,
                This\Helper\ExceptionHelper::PROPERTY_NOT_FOUND
            );
        }
        try {
            $valueParsed = This\Helper\ParseValueHelper::parseValue($value, $propertyType);
        } catch (Exception\TypeMismatchException $exception) {
            throw new Exception\TypeMismatchException(
                'Invalid type, expected: `' . $propertyType . '` or ArrayObject for [' . $key . '] @' . static::class,
                $exception->getCode() ?? This\Helper\ExceptionHelper::TYPE_MISMATCH,
                $exception
            );
        }
        if (!is_callable($valueParsed, false, $callableName)
            || $callableName !== 'Closure::__invoke' ) {
            // This case happens when a closure - anonymus function - is inserted
            // There is no way to tell if toe closures are different functions, so it will always overwrite the key
            if (isset($this->{$key})
                && This\Helper\CompareValueHelper::hasSameData($this->{$key}, $valueParsed)) {
                return $this;
            }
        }
        $this->{$key} = $valueParsed;
        if (!$isSilent) {
            $this->addFieldToDirty($key);
        }
        return $this;
    }
}