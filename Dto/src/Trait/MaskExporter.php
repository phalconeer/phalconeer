<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait MaskExporter
{
    use This\Trait\ArrayObjectExporter;

    public function exportWithMask(
        array $mask
    ) : \ArrayObject
    {
        $toShow = This\Transformer\MaskExporter::maskToAlias($mask);

        foreach ($this->properties() as $property) {
            if (!in_array($property, $toShow)) {
                $aliases[$property] = null;
            }
        }

        return This\Transformer\AliasExporter::exportAliasesWithArray(
            $this->toArrayObject(),
            $aliases
        );
    }
}