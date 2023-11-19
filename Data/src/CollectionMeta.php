<?php
namespace Phalconeer\Data;

class CollectionMeta implements CollectionMetaInterface
{
    /**
     * When an item is cleared form the object and default value has to be saved, include the property name here.
     */
    protected bool $isDirty = false;
    
    public function isDirty() : bool
    {
        return $this->isDirty;
    }

    public function setDirty(bool $isDirty) : self
    {
        $this->isDirty = $isDirty;
        return $this;
    }
}