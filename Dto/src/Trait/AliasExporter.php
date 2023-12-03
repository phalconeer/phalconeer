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

    public static function maskToAlias($mask) : array
    {
        $aliases = array_reduce(
            $mask,
            function($aggregator, $currentMask) {
                $property = explode('.', $currentMask, 2);
                if (array_key_exists(1, $property)) {
                    if (!array_key_exists($property[0], $aggregator)) {
                        $aggregator[$property[0]] = [];
                    }
                    $aggregator[$property[0]][] = $property[1];
                } else {
                    $aggregator[$currentMask] = $currentMask;
                }
                return $aggregator;
            },
            []
        );

        foreach ($aliases as $property => $alias) {
            if (is_array($alias)) {
                $aliases[$property] = self::maskToAlias($alias);
            }
        }

        return $aliases;
    }

    public function exportAliasesWithMask(
        array $mask
    ) : \ArrayObject
    {
        $toShow = self::maskToAlias($mask);

        foreach ($this->properties() as $property => $type) {
            if (!in_array($property, $toShow)) {
                $aliases[$property] = null;
            }
        }

        return self::exportAliasesWithArray(
            $this->toArrayObject(),
            $aliases
        );
    }
}