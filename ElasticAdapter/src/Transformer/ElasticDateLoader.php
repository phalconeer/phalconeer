<?php
namespace Phalconeer\ElasticAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class ElasticDateLoader implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'loadAllElasticDate';

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
            $parameters->offsetSet('dateProperties', self::getDateProperties($baseObject));
        }
        return self::loadAllElasticDate($source, $parameters);
    }

    public static function getDateProperties(Data\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                return $type === \DateTime::class;
            }
        );
    }

    public static function loadAllElasticDate(
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
            if (in_array($iterator->key(), $dateProperties)) {
                $source->offsetSet(
                    $iterator->key(),
                    self::loadElasticDate($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function loadElasticDate(
        $date
    ) : ?\DateTime 
    {
        if (is_null($date)
            || $date instanceof \DateTime) {
            return $date;
        }

        return new \DateTime($date);
    }
}