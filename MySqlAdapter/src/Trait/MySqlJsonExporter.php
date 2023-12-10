<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\MySqlAdapter as This;

trait MySqlJsonExporter
{
    use Dto\Trait\ArrayObjectExporter;

    public function exportAllMySqlJson() : \ArrayObject 
    {
        return This\Transformer\MySqlJsonExporter::exportAllMySqlJson(
            $this->toArrayObject(),
            new \ArrayObject([
                'jsonProperties'    => Data\Helper\ParseValueHelper::getNestedProperties($this)
            ])
        );
    }
}