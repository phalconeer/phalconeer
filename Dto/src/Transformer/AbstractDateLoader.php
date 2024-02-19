<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

abstract class AbstractDateLoader implements Dto\TransformerInterface
{
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
        if (!$parameters->offsetExists('dateProperties')) {
            $parameters->offsetSet('dateProperties', Data\Helper\ParseValueHelper::getDateProperties($baseObject));
        }
        return static::loadAllDate($source, $parameters);
    }

    public static function loadAllDate(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $dateProperties = (is_null($parameters)
            || !$parameters->offsetExists('dateProperties'))
            ? []
            : $parameters->offsetGet('dateProperties');
        while ($iterator->valid()) {
            if (array_key_exists($iterator->key(), $dateProperties)) {
                $source->offsetSet(
                    $iterator->key(),
                    static::convertDate($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    abstract public static function convertDate(
        $date
    ) : ?\DateTime;
}