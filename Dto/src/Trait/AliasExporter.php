<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait AliasExporter
{
    use This\Trait\ArrayObjectExporter;

    public function exportAliases() : \ArrayObject 
    {
        return self::exportAliasesWithArray(
            $this->toArrayObject(),
            $this->transformer->exportAliases()
        );
    }

    public static function exportAliasesWithArray(
        \ArrayObject $source,
        array $aliases
    ) : \ArrayObject 
    {
        return This\Transformer\AliasExporter::exportAliasesWithArray(
            $source,
            $aliases
        );
    }

    public static function maskArrayObject(
        \ArrayObject $source,
        array $aliases = null
    ) : \ArrayObject
    {
        return self::exportAliasesWithArray(
            $source,
            $aliases
        );
    }

    public function maskArray(
        array $source,
        array $aliases = null
    ) : array
    {
        return self::exportAliasesWithArray(
            new \ArrayObject($source),
             $aliases
        )->getArrayCopy();
    }
}