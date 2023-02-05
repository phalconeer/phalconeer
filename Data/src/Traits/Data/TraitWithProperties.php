<?php
namespace Phalconeer\Data\Traits\Data;


trait TraitWithProperties
{
    protected function initializeDataTrait(\ArrayObject $inputObject) : \ArrayObject 
    {
        return $inputObject;
    }

    protected function initializeData(\ArrayObject $inputObject) : \ArrayObject 
    {
        return $this->initializeDataTrait($inputObject); 
        //This is the default if initializeData is not defined in the class where the trait is used
    }
}