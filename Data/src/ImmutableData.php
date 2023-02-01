<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;

abstract class ImmutableObject implements This\DataInterface
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
    protected bool $_stored = false;

    /**
     * Recursive loads internal properties crawling up the inheritence tree
     */
    public static function getInternalProperties() : array
    {
        $parentClassName = get_parent_class(static::class);
        return method_exists($parentClassName, __FUNCTION__) ? 
            array_merge($parentClassName::getInternalProperties(), static::$_internalProperties) : 
            static::$_internalProperties;
    }

    /**
     * Recursive loads defined properties crawling up the inheritence tree
     */
    public static function getProperties(array $baseProperties = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return method_exists($parentClassName, __FUNCTION__) ? 
            array_merge($parentClassName::getProperties(), static::$_properties, $baseProperties) : 
            array_merge(static::$_properties, $baseProperties);
    }

    /**
     * This function parses the types of the objects.
     * It can be overwritten by ParseTypesTrait, in which case the the PHPDoc of the Obejct is aprsed to get the types.
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
        if (is_null($this->{$propertyName})) {
            return null;
        }

        if (!array_key_exists($propertyName, $this->_propertiesCache)) {
            return null;
        }

        if (ParseValueHelper::isSimpleValue($this->_propertiesCache[$propertyName])
            || ($this->_propertiesCache[$propertyName] === AnyProperty::class
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
     *
     * @param boolean $convertChildren
     * @param boolean $copyNullsAsDefaults
     * @return mixed
     */
    public function getPrimaryKeyValue(bool $convertChildren = true, bool $copyNullsAsDefaults = true) : array
    {
        $primaryKey = $this->getPrimaryKey();
        return array_map(
            function ($attribute) use ($convertChildren, $copyNullsAsDefaults) {
                return $this->getValue(
                    $attribute,
                    $convertChildren,
                    $copyNullsAsDefaults
                );
            },
            $primaryKey
        );
    }

    /**
     * Takes in an object with new values and merges them with the existing data..
     * The object internals are not updated, a new instance is returned.
     */
    public function applyChange(self $changes) : self
    {
        $oldArray = $this->toArrayCopy(false, false);
        $newArray = $changes->toArrayCopy(false, false);

        return new static(array_merge($oldArray, $newArray));
    }
    /**
     * Updates a field and return a new instance of the object
     */
    public function setKeyValue(string $key, $value, $isSilent = false) : self
    {
        if (!property_exists($this, $key)) {
            throw new InvalidArgumentException('Object property does not exist: ' . $key . ' @ ' . static::class, ExceptionHelper::PROPERTY_NOT_FOUND);
        }
        if (is_object($value)) {
            $value = clone($value);
            switch (true) {
                case $value instanceof ImmutableObject:
                    $valueToCompare = $value->toArrayCopy();
                default:
                    // TODO: make this smarter....
                    $valueToCompare = 'UNABLE TO TRANSFORM';
            }
        } else {
            $valueToCompare = $value;
        }

        $oldArray = $this->toArrayCopy(false, false);
        $oldValue = (array_key_exists($key, $oldArray))
            ? $this->getValue($key)
            : null;
        $oldArray[$key] = $value;
        $new = new static($oldArray);
        $new->setDirty($this->_dirty);
        if ($oldValue != $valueToCompare
            && !$isSilent) {
            $new->addFieldToDirty($key);
        }
        return $new;
    }

    /**
     * Read the types of class properties
     *
     * @return array
     */
    public function properties() : array
    {
        return $this->_propertiesCache;
    }

    /**
     * Returns which fields has to be cleared on saving
     * 
     * @return array
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