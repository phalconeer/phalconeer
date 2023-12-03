<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class AliasExporter implements This\TransformerInterface
{
    const TRAIT_METHOD = 'exportAliases';

    public function __construct(public array $exportAliases)
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if (is_array($source)) {
            $source = new \ArrayObject($source);
        }
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        return self::exportAliasesWithArray(
            $source,
            $this->exportAliases
        );
    }

    public static function exportAliasesWithArray(
        \ArrayObject $source,
        array $aliases
    ) : \ArrayObject 
    {
        if (count($aliases) === 0) {
            return $source;
        }
        foreach ($aliases as $internalProperty => $externalProperty) {
            if ($source->offsetExists($internalProperty)) {
                if (!is_null($externalProperty)) {
                    $value = $source->offsetGet($internalProperty);
                    if (is_object($value)) {
                        $value = clone($value);
                        if (is_array($externalProperty)
                            && $value instanceof This\ArrayObjectExporterInterface) {
                            $value = self::exportAliasesWithArray(
                                $value->toArrayObject(),
                                $externalProperty
                            );
                        }
                    }
                    $source->offsetSet(
                        $externalProperty,
                        $value
                    );
                }
                $source->offsetUnset($internalProperty);
            }
        }
        return $source;
    }
}