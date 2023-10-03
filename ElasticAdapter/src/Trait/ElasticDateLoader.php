<?php
namespace Phalconeer\ElasticAdapter\Trait;

use Phalconeer\ElasticAdapter as This;

trait ElasticDateLoader
{
    public function loadAllElasticDate(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return This\Transformer\ElasticDateLoader::loadAllElasticDate(
            $inputObject,
            new \ArrayObject([
                'dateProperties'    => This\Transformer\ElasticDateLoader::getDateProperties($this)
            ])
        );
    }
}