<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\MySqlAdapter as This;

trait MySqlBooleanExporter
{
    use Dto\Trait\ArrayObjectExporter;

    public function exportAllMySqlBoolean() : \ArrayObject 
    {
        return This\Transformer\MySqlBooleanExporter::exportAllMySqlBoolean(
            $this->toArrayObject(),
            new \ArrayObject([
                'boolProperties'    => Data\Helper\ParseValueHelper::getBoolProperties($this)
            ])
        );
    }
}