<?php
namespace Phalconeer\Data;

interface MetaInterface 
{
    public function addFieldToDirty(string $field) : self;

    public function dirty() : array;

    public function doesPropertyExist(string $field) : bool;

    public function getFields() : array;

    public function isDirty(string $field) : bool;

    public function propertiesCache() : array;

    public function propertyType(string $field) : null | string | array;

    public function setDirty(array $dirtyFields) : self;

    public function setPropertiesCache(array $propertiesCache) : self;

    public function setStored(bool $stored) : self;

    public function stored() : bool;
}