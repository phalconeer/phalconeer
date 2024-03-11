<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Dto as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class MaskExporter implements Dto\TransformerVariableInterface
{
    public function __construct(
        protected array $mask
    )
    {
        
    }

    public static function maskToAlias($mask) : array
    {
        $aliases = [];
        foreach ($mask as $currentMask) {
            $property = explode('.', $currentMask, 2);
            if (array_key_exists(1, $property)) {
                if (!array_key_exists($property[0], $aliases)) {
                    $aggregator[$property[0]] = [];
                }
                $aggregator[$property[0]][] = $property[1];
            } else {
                $aggregator[$currentMask] = $currentMask;
            }
        }

        foreach ($aliases as $property => $alias) {
            if (is_array($alias)) {
                $aliases[$property] = self::maskToAlias($alias);
            }
        }

        return $aliases;
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : \ArrayObject
    {
        $toShow = self::maskToAlias($this->mask);

        foreach ($baseObject->properties() as $property) {
            if (!in_array($property, $toShow)) {
                $aliases[$property] = null;
            }
        }
        if (!$baseObject instanceof Dto\ArrayObjectExporterInterface) {
            return $source;
        }

        return This\Transformer\AliasExporter::exportAliasesWithArray(
            $baseObject->toArrayObject(),
            $aliases
        );
    }
}