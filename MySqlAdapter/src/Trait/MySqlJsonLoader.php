<?php
namespace Phalconeer\MySqlAdapter\Trait;

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
                'jsonProperties'    => This\Transformer\MySqlJsonLoader::getJsonProperties($this)
            ])
        );
    }
}