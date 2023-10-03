<?php
namespace Phalconeer\ElasticAdapter\Trait;

use Phalconeer\ElasticAdapter as This;
use Phalconeer\Dto;

trait ElasticDateExporter
{
    use Dto\Trait\ArrayObjectExporter;

    public function exportAllElasticDate() : \ArrayObject 
    {
        return This\Transformer\ElasticDateExporter::exportAllElasticDate(
            $this->toArrayObject(),
            new \ArrayObject([
                'dateProperties'    => This\Transformer\ElasticDateLoader::getDateProperties($this)
            ])
        );
    }
}