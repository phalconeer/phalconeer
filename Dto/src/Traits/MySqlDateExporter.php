<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Dto as This;

trait MySqlDateExporter
{
    use This\Traits\ArrayObjectExporter;

    public function exportMySqlDate() : \ArrayObject 
    {
        return This\Transformer\MySqlDateExporter::exportMySqlDate(
            $this->toArrayObject(),
        );
    }
}