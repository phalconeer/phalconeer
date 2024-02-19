<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

abstract class AbstractDateExporter implements Dto\TransformerInterface
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
        if (!is_null($baseObject)) {
            $parameters->offsetSet('dateProperties', Data\Helper\ParseValueHelper::getDateProperties($baseObject));
        }
        return static::exportAllDate($source, $parameters);
    }

    public static function exportAllDate(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $dateProperties = ($parameters?->offsetExists('dateProperties'))
            ? $parameters->offsetGet('dateProperties')
            : null;
        while ($iterator->valid()) {
            if ((is_null($dateProperties)
                    || array_key_exists($iterator->key(), $dateProperties))
                && $iterator->current() instanceof \DateTime) {
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
        \DateTime $date
    );
}