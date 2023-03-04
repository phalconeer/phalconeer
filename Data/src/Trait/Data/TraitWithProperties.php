<?php
namespace Phalconeer\Data\Trait\Data;


trait TraitWithProperties
{
    public function initializeDataTrait(\ArrayObject $inputObject) : \ArrayObject 
    {
        return $inputObject;
    }

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject 
    {
        return $this->initializeDataTrait($inputObject); 
        //This is the default if initializeData is not defined in the class where the trait is used
    }
}