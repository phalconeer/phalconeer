<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class AliasExporter implements This\TransformerInterface
{
    public function __construct(protected array $exportAliases)
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source = null,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if (is_null($source)) {
            return $source;
        }
        $source = This\Transformer\ArrayObjectExporter::normalizeArrayObject($source);
        if (!$source instanceof \ArrayObject) {
            return $source;
        }

        return self::exportAliasesWithArray(
            $source,
            $parameters?->offsetGet('aliases') ?? $this->exportAliases
        );
    }

    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = This\Transformer\ArrayObjectExporter::normalizeArrayObject($source);
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        return self::exportAliasesWithArray(
            $source,
            $parameters?->offsetGet('aliases') ?? $baseObject->transformer->exportAliases()
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