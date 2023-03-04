<?php
namespace Phalconeer\MySqlAdapter\Trait;

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
                'jsonProperties'    => This\Transformer\MySqlJsonLoader::getJsonProperties($this)
            ])
        );
    }
}