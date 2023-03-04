<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\MySqlAdapter as This;

trait MySqlBooleanLoader
{
    public function loadAllMySqlBoolean(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return This\Transformer\MySqlBooleanLoader::loadAllMySqlBoolean(
            $inputObject,
            new \ArrayObject([
                'boolProperties'    => This\Transformer\MySqlBooleanLoader::getBoolProperties($this)
            ])
        );
    }
}