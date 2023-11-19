<?php
namespace Phalconeer\ElasticAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\ElasticAdapter as This;

class ElasticDateExporter implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllElasticDate';

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
            $parameters->offsetSet('dateProperties', This\Transformer\ElasticDateLoader::getDateProperties($baseObject));
        }
        return self::exportAllElasticDate($source, $parameters);
    }

    public static function exportAllElasticDate(
        \ArrayObject $source
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            if ($iterator->current() instanceof \DateTime) {
                $source->offsetSet(
                    $iterator->key(),
                    self::exportElasticDate($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function exportElasticDate(
        \DateTime $date
    ) : string 
    {
        return $date->format('c');
    }
}