<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlDateExporter implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllMySqlDate';

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
            $parameters->offsetSet('dateProperties', MySqlDateLoader::getDateProperties($baseObject));
        }
        return self::exportAllMySqlDate($source, $parameters);
    }

    public static function exportAllMySqlDate(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $dateProperties = (is_null($parameters)
            || !$parameters->offsetExists('dateProperties'))
            ? null
            : $parameters->offsetGet('dateProperties');
        while ($iterator->valid()) {
            if ((is_null($dateProperties)
                    || array_key_exists($iterator->key(), $dateProperties))
                && $iterator->current() instanceof \DateTime) {
                $source->offsetSet(
                    $iterator->key(),
                    self::exportMySqlDate($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function exportMySqlDate(
        \DateTime $date
    ) : string 
    {
        return $date->format('Y-m-d H:i:s');
    }
}