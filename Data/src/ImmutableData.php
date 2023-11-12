<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;
use Phalconeer\Exception;

abstract class ImmutableData implements This\DataInterface
{
    public This\MetaInterface $meta;

    /**
     * List of all fields in the object and their associated type.
     */
    protected static array $properties = [
        // property => type
    ];

    /**
     * Loads data from either an array or ArrayObject
     * @TODO: include the behavior of Default properties, which help differentiate between null values and deleted values.
     */
    public function __construct(\ArrayObject $inputObject = null)
    {
        if (!isset($this->meta)
            || is_null($this->meta)) {
            $this->meta = new This\DataMeta();
        }
        $this->meta->setPropertiesCache($this->parseTypes(static::getProperties()));
        if (is_null($inputObject)) {
            $inputObject = new \ArrayObject();
        }
        $inputObject = $this->initializeData($inputObject);
        foreach ($this->meta->propertiesCache() as $propertyName => $propertyType) {
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
     * Recursive loads defined properties crawling up the inheritence tree
     */
    public static function getProperties(array $baseProperties = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge($parentClassName::getProperties(), static::$properties, $baseProperties) : 
            array_merge(static::$properties, $baseProperties);
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
     * Use transformer traits for generic cases (export functions has to be created in the object)
     */
    public function getValue(string $propertyName)
    {
        if (!isset($this->{$propertyName}) // Added as with typed properties, the object can be in uninitialzed state, which throws property "must not be accessed before initialization"
            || is_null($this->{$propertyName})
            || !$this->meta->doesPropertyExist($propertyName)) {
            return null;
        }

        $propertyType = $this->meta->propertyType($propertyName);

        if (This\Helper\ParseValueHelper::isSimpleValue($propertyType)
            || ($propertyType === This\Property\Any::class
                && !is_object($this->{$propertyName}))) {
            return $this->{$propertyName};
        } else {
            return clone($this->{$propertyName});
        }
    }

    /**
     * Returns all the values in an ArrayObject
     */
    public function getValues() : \ArrayObject
    {
        $data = new \ArrayObject();
        foreach ($this->properties() as $property => $propertyType) {
            $data->offsetSet($property, $this->getValue($property));
        }
        return $data;
    }

    /**
     * Calculates which is the primary key for the object. By default it will the first defined property in the object.
     * Mostly used in updating MySQL tables or figuring out uniqueness.
     *
     */
    public function getPrimaryKey() : array
    {
        $arrayKeys = $this->meta->getFields();
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
            // There is no way to tell if two closures are different functions, so it will always overwrite the key
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
            $new->meta->addFieldToDirty($key);
        }
        return $new;
    }

    /**
     * Read the types of class properties
     */
    public function properties() : array
    {
        return $this->meta->getFields();
    }

    /**
     * Read the types of class properties
     */
    public function propertyTypes() : array
    {
        return $this->meta->propertiesCache();
    }

    /**
     * Read the types of class properties
     */
    public function propertyType(string $key) : ?string
    {
        return $this->meta->propertyType($key);
    }

    public function countNotNulls() : int
    {
        $notNulls = 0;
        foreach ($this->meta->getFields() as $property) {
            if (!is_null($this->{$property})) {
                $notNulls++;
            }
        }
        return $notNulls;
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
                return $aggergator
                    && isset($this->{$attribute})
                    && !is_null($this->{$attribute});
            },
            true
        );
    }
}