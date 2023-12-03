<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait BooleanByKeyLoader
{
    public function loadAllBooleanByKey(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return This\Transformer\BooleanByKeyLoader::loadAllBooleanByKey(
            $inputObject,
            new \ArrayObject([
                'boolProperties'    => Data\Helper\ParseValueHelper::getBoolProperties($this)
            ])
        );
    }
}