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
            $parameters->offsetSet('boolProperties', Data\Helper\ParseValueHelper::getBoolProperties($baseObject));
        }
        return self::loadAllMySqlBoolean($source, $parameters);
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
            if (array_key_exists($iterator->key(), $boolProperties)) {
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