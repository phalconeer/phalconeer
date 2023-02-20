<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait ElasticDateExporter
{
    use This\Trait\ArrayObjectExporter;

    public function exportAllElasticDate() : \ArrayObject 
    {
        return This\Transformer\ElasticDateExporter::exportAllElasticDate(
            $this->toArrayObject(),
        );
    }
}