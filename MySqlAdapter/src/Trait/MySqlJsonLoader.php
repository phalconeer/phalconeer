<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Data;
use Phalconeer\MySqlAdapter as This;

trait MySqlJsonLoader
{
    public function loadAllMySqlJson(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return This\Transformer\MySqlJsonLoader::loadAllMySqlJson(
            $inputObject,
            new \ArrayObject([
                'jsonProperties'    => Data\Helper\ParseValueHelper::getNestedProperties($this)
            ])
        );
    }
}