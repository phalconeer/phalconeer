<?php
namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\MySqlAdapter as This;

trait MySqlDateExporter
{
    use Dto\Trait\ArrayObjectExporter;

    public function exportAllMySqlDate() : \ArrayObject 
    {
        return This\Transformer\MySqlDateExporter::exportAllMySqlDate(
            $this->toArrayObject(),
            new \ArrayObject([
                'dateProperties'    => Data\Helper\ParseValueHelper::getDateProperties($this)
            ])
        );
    }
}