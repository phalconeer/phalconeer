<?php
namespace Phalconeer\MySqlAdapter\Trait;

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
                'dateProperties'    => This\Transformer\MySqlDateLoader::getDateProperties($this)
            ])
        );
    }
}