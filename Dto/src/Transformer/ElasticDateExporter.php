<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ElasticDateExporter implements This\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllElasticDate';

    public function transform(
        $source,
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
        return self::exportAllElasticDate($source);
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