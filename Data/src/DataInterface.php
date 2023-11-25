<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;

interface DataInterface extends CommonInterface
{
    public function getValue(string $propertyName);

    public function getValues() : \ArrayObject;

    public function getPrimaryKey() : array;

    public function getPrimaryKeyValue() : array;

    public function properties() : array;

    public function propertyTypes() : array;

    public function setValueByKey(
        string $key,
        $value,
        $isSilent = false
    ) : self;

    public function merge(This\DataInterface $changes) : This\DataInterface;

    public function isStored() : bool;
}