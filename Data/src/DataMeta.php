<?php
namespace Phalconeer\Data;

class DataMeta implements MetaInterface
{
    /**
     * When an item is cleared form the object and default value has to be saved, include the property name here.
     */
    protected array $dirty = [];
    
    /**
     * Use this array to save computing time and cache the properties
     */
    protected array $propertiesCache = [
        // property => type
    ];

    /**
     * This property can be used to control if saving this needs to create a new record or update an existing.
     * By default it is overwritten by checking the value of the primary key.
     */
    protected ?bool $stored = false;

    public function addFieldToDirty(string $field) : self
    {
        if (in_array($field, $this->dirty)) {
            return $this;
        }
        $this->dirty[] = $field;
        return $this;
    }

    public function dirty() : array
    {
        return $this->dirty;
    }

    public function doesPropertyExist(string $field) : bool
    {
        return array_key_exists($field, $this->propertiesCache);
    }

    public function getFields() : array
    {
        return array_keys($this->propertiesCache);
    }

    public function isDirty(string $field) : bool
    {
        return in_array($field, $this->dirty);
    }

    public function propertiesCache() : array
    {
        return $this->propertiesCache;
    }

    public function propertyType(string $field) : ?string
    {
        return ($this->doesPropertyExist($field))
            ? $this->propertiesCache[$field]
            : null;
    }

    public function setDirty(array $dirtyFields) : self
    {
        $this->dirty = $dirtyFields;
        return $this;
    }

    public function setPropertiesCache(array $propertiesCache) : self
    {
        $this->propertiesCache = $propertiesCache;
        return $this;
    }

    public function setStored(bool $stored) : self
    {
        $this->stored = $stored;
        return $this;
    }

    public function stored() : bool
    {
        return $this->stored;
    }
}