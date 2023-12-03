<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlBooleanExporter implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllMySqlBoolean';

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
        if (!is_null($baseObject)) {
            $parameters->offsetSet('boolProperties', Data\Helper\ParseValueHelper::getBoolProperties($baseObject));
        }
        }
        return self::exportAllMySqlBoolean($source);
    }

    public static function exportAllMySqlBoolean(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $boolProperties = (is_null($parameters)
            || !$parameters->offsetExists('boolProperties'))
            ? null
            : $parameters->offsetGet('boolProperties');
        while ($iterator->valid()) {
            if ((is_null($boolProperties)
                    || array_key_exists($iterator->key(), $boolProperties))
                && is_bool($iterator->current())) {
                $source->offsetSet(
                    $iterator->key(),
                    self::exportMySqlBoolean($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function exportMySqlBoolean(
        bool $boolean = null
    ) : string 
    {
        return (is_null($boolean)) ? null : (($boolean === true) ? 1 : 0);
    }
}