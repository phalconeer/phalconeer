<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlJsonLoader implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'loadAllMySqlJson';

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
            $parameters->offsetSet('jsonProperties', self::getJsonProperties($baseObject));
        }
        return self::loadAllMySqlJson($source, $parameters);
    }

    public static function getJsonProperties(Data\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                return is_subclass_of($type, Dto\ImmutableDto::class);
            }
        );
    }

    public static function loadAllMySqlJson(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $jsonProperties = (is_null($parameters)
            || !$parameters->offsetExists('jsonProperties'))
            ? []
            : $parameters->offsetGet('jsonProperties');
        while ($iterator->valid()) {
            if (array_key_exists($iterator->key(), $jsonProperties)
                && !is_null($iterator->current())) {
                $source->offsetSet(
                    $iterator->key(),
                    new \ArrayObject(self::loadMySqlJson($iterator->current()))
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function loadMySqlJson(
        $json
    ) : ?array 
    {
        if (is_null($json)) {
            return null;
        }
        return json_decode($json, 1);
    }
}