<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;
use Phalconeer\Exception;

abstract class ImmutableData implements This\DataInterface
{
    /**
     * List of all the protected fields which do not contain data
     */
    protected static array $_internalProperties = [
        '_internalProperties',
        '_properties',
        '_propertiesCache',
        '_dirty',
        '_stored'
    ];

    /**
     * List of all fields in the object and their associated type.
     */
    protected static array $_properties = [
        // property => type
    ];

    /**
     * Use this array to save computing time and cache the properties
     */
    protected array $_propertiesCache = [
        // property => type
    ];

    /**
     * When an item is cleared form the object and default value has to be saved, include the property name here.
     */
    protected array $_dirty = [];

    /**
     * This property can be used to control if saving this needs to create a new record or update an existing.
     * By default it is overwritten by checking the value of the primary key.
     */
    protected ?bool $_stored = false;

    /**
     * Loads data from either an array or ArrayObject
     * @TODO: include the behavior of Default properties, which help differentiate between null values and deleted values.
     */
    public function __construct(\ArrayObject $inputObject = null)
    {

        if (is_null($inputObject)) {
            $inputObject = new \ArrayObject();
        }
        $inputObject = $this->initializeData($inputObject);
        $this->_propertiesCache = $this->parseTypes(static::getProperties());
        foreach ($this->_propertiesCache as $propertyName => $propertyType) {
            if (!$inputObject->offsetExists($propertyName)) {
                continue;
            }
            try {
                $this->{$propertyName} = This\Helper\ParseValueHelper::parseValue(
                    $inputObject->offsetGet($propertyName),
                    $propertyType
                );
            } catch (Exception\TypeMismatchException $exception) {
                throw new Exception\TypeMismatchException(
                    'Invalid type, expected: `' . $propertyType . '` or ArrayObject for [' . $propertyName . '] @' . static::class,
                    $exception->getCode() ?? This\Helper\ExceptionHelper::TYPE_MISMATCH,
                    $exception
                );
            }
        }
    }

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject
    {
        return $inputObject;
    }

    /**
     * Recursive loads internal properties crawling up the inheritence tree
     */
    public static function getInternalProperties() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge($parentClassName::getInternalProperties(), static::$_internalProperties) : 
            static::$_internalProperties;
    }

    /**
     * Recursive loads defined properties crawling up the inheritence tree
     */
    public static function getProperties(array $baseProperties = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge($parentClassName::getProperties(), static::$_properties, $baseProperties) : 
            array_merge(static::$_properties, $baseProperties);
    }

    /**
     * This function parses the types of the objects.
     * It can be overwritten by ParseTypes, in which case the the PHPDoc of the Obejct is aprsed to get the types.
     */
    protected function parseTypes(array $predefinedProperties) : array
    {
        return $predefinedProperties;
    }

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

        if (This\Helper\ParseValueHelper::isSimpleValue($this->_propertiesCache[$propertyName])
            || ($this->_propertiesCache[$propertyName] === This\Property\Any::class
                && !is_object($this->{$propertyName}))) {
            return $this->{$propertyName};
        } else {
            return clone($this->{$propertyName});
        }
    }

    /**
     * Calculates which is the primary key for the object. By default it will the first defined property in the object.
     * Mostly used in updating MySQL tables or figuring out uniqueness.
     *
     */
    public function getPrimaryKey() : array
    {
        $arrayKeys = array_keys($this->_propertiesCache);
        return array_slice($arrayKeys, 0, 1);
    }

    /**
     * Returns the value of the primary key
     */
    public function getPrimaryKeyValue() : array
    {
        $primaryKey = $this->getPrimaryKey();
        return array_map(
            function ($attribute) {
                return $this->getValue($attribute);
            },
            $primaryKey
        );
    }

    /**
     * Takes in an object with new values and merges them with the existing data..
     * The object internals are not updated, a new instance is returned.
     */
    public function merge(This\DataInterface $changes) : This\DataInterface
    {
        $new = clone($this);
        foreach ($changes->properties() as $propertyName) {
            if (isset($changes->{$propertyName})
                && !is_null($changes->{$propertyName})) {
                $new = $new->setValueByKey(
                    $propertyName,
                    $changes->{$propertyName}
                );
            }
        }

        return $new;
    }

    public function doesValueNeedUpdate($key, $value)
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
                'Invalid type, expected: `' . $propertyType . '` or array for [' . $key . '] @' . static::class,
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
                'Invalid type, expected: `' . $propertyType . '` or array for [' . $key . '] @' . static::class,
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
        $new = clone($this);
        $new->{$key} = $valueParsed;
        if (!$isSilent) {
            $new->addFieldToDirty($key);
        }
        return $new;
    }

    /**
     * Read the types of class properties
     */
    public function properties() : array
    {
        return array_keys($this->_propertiesCache);
    }

    /**
     * Read the types of class properties
     */
    public function propertyTypes() : array
    {
        return $this->_propertiesCache;
    }

    /**
     * Read the types of class properties
     */
    public function propertyType(string $key) : ?string
    {
        if (!array_key_exists($key, $this->_propertiesCache)) {
            return null;
        }
        return $this->_propertiesCache[$key];
    }

    /**
     * Returns which fields has to be cleared on saving
     */
    public function addFieldToDirty(string $field) : self
    {
        if (in_array($field, $this->_dirty)) {
            return $this;
        }
        $this->_dirty[] = $field;
        return $this;
    }

    public function setDirty(array $dirtyFields) : self
    {
        $this->_dirty = $dirtyFields;
        return $this;
    }

    /**
     * Returns which fields has to be cleared on saving
     */
    public function dirty() : array
    {
        return $this->_dirty;
    }

    /**
     * Returns if the current version is different than the original
     */
    public function isDirty() : bool
    {
        return count($this->_dirty) > 0;
    }

    public function countNotNulls() : int
    {
        $notNulls = 0;
        foreach ($this->_propertiesCache as $property => $type) {
            if (!is_null($this->{$property})) {
                $notNulls++;
            }
        }
        return $notNulls;
    }

    public function setStored(bool $stored) : self
    {
        $this->_stored = $stored;
        return $this;
    }

    /**
     * By default a data object is considered stored if the primary keys are set
     */
    public function isStored() : bool
    {
        $primaryKey = $this->getPrimaryKey();
        return array_reduce(
            $primaryKey,
            function ($aggergator, $attribute) {
                return $aggergator && !is_null($this->{$attribute});
            },
            true
        );
    }
}