<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlJsonLoader implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        if (is_null($parameters)) {
            $parameters = new \ArrayObject();
        }
        if (!$parameters->offsetExists('dateProperties')
            && !is_null($baseObject)) {
            $parameters->offsetSet('jsonProperties', Data\Helper\ParseValueHelper::getNestedProperties($baseObject));
        }
        return self::loadAllMySqlJson($source, $parameters);
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
        // This is to help creating the objects from the application
        if (is_array($json)) {
            return $json;
        }
        if ($json instanceof \ArrayObject) {
            return $json->getArrayCopy();
        }
        
        return json_decode($json, 1);
    }
}