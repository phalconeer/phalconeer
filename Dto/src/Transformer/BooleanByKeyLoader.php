<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class BooleanByKeyLoader implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        if (is_null($parameters)) {
            $parameters = new \ArrayObject();
        }
        if (!$parameters->offsetExists('dateProperties')
            && !is_null($baseObject)) {
            $parameters->offsetSet('boolProperties', Data\Helper\ParseValueHelper::getBoolProperties($baseObject));
        }
        return self::loadAllBooleanByKey($source, $parameters);
    }

    public static function loadAllBooleanByKey(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $boolProperties = (is_null($parameters)
            || !$parameters->offsetExists('boolProperties'))
            ? []
            : $parameters->offsetGet('boolProperties');
        $sourceData = $source->getArrayCopy();
        foreach ($boolProperties as $boolProperty => $type) {
            if (array_key_exists($boolProperty, $sourceData)) {
                $source->offsetSet(
                    $boolProperty,
                    $sourceData[$boolProperty]
                );
            } else {
                $source->offsetSet(
                    $boolProperty,
                    in_array($boolProperty, $sourceData)
                );
            }
        }
        return $source;
    }
}