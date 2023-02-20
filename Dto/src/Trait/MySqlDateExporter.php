<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait MySqlDateExporter
{
    use This\Trait\ArrayObjectExporter;

    public function exportAllMySqlDate() : \ArrayObject 
    {
        return This\Transformer\MySqlDateExporter::exportAllMySqlDate(
            $this->toArrayObject(),
        );
    }
}