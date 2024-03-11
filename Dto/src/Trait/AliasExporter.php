<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait AliasExporter
{
    use This\Trait\ArrayObjectExporter;

    public function exportAliases(
        array $aliases = null
    ) : \ArrayObject 
    {
        return self::exportAliasesWithArray(
            $this->toArrayObject(),
            $aliases ?? $this->transformer->exportAliases()
        );
    }
}