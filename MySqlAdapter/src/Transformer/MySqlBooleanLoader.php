<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlBooleanLoader implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'loadAllMySqlBoolean';

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
        if (is_null($parameters)) {
            $parameters = new \ArrayObject();
        }
        if (!is_null($baseObject)) {
            $parameters->offsetSet('boolProperties', self::getBoolProperties($baseObject));
        }
        return self::loadAllMySqlBoolean($source, $parameters);
    }

    public static function getBoolProperties(Data\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                return $type === Data\Helper\ParseValueHelper::TYPE_BOOL
                    || $type === Data\Helper\ParseValueHelper::TYPE_BOOLEAN;
            }
        );
    }

    public static function loadAllMySqlBoolean(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $boolProperties = (is_null($parameters)
            || !$parameters->offsetExists('boolProperties'))
            ? []
            : $parameters->offsetGet('boolProperties');
        while ($iterator->valid()) {
            if (in_array($iterator->key(), $boolProperties)) {
                $source->offsetSet(
                    $iterator->key(),
                    self::loadMySqlBoolean($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function loadMySqlBoolean(
        $boolean
    ) : bool 
    {
        return ((int) $boolean === 1);
    }
}