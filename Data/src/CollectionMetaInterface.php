<?php
namespace Phalconeer\Data;

interface CollectionMetaInterface 
{
    public function isDirty() : bool;

    public function setDirty(bool $isDirty) : self;
}