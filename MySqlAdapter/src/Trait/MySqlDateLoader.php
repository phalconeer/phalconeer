<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Data;
use Phalconeer\MySqlAdapter as This;

trait MySqlDateLoader
{
    public function loadAllMySqlDate(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return This\Transformer\MySqlDateLoader::loadAllMySqlDate(
            $inputObject,
            new \ArrayObject([
                'dateProperties'    => Data\Helper\ParseValueHelper::getDateProperties($this)
            ])
        );
    }
}