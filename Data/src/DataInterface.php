<?php
namespace Phalconeer\Data;

interface DataInterface extends CommonInterface
{
    public function dirty() : array;

    public function getPrimaryKey() : array;

    public function getPrimaryKeyValue() : array;

    public function isStored() : bool;

    public function setValueByKey(
        string $key,
        $value,
        $isSilent = false
    ) : self;
}