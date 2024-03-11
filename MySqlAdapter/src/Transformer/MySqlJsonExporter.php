<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlJsonExporter implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);
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
        return self::exportAllMySqlJson($source);
    }

    public static function exportAllMySqlJson(
        \ArrayObject $source,
        \ArrayObject $parameters = null
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        $jsonProperties = (is_null($parameters)
            || !$parameters->offsetExists('jsonProperties'))
            ? null
            : $parameters->offsetGet('jsonProperties');
        while ($iterator->valid()) {
            if ((is_null($jsonProperties)
                    || array_key_exists($iterator->key(), $jsonProperties))
                && is_object($iterator->current())) {
                $source->offsetSet(
                    $iterator->key(),
                    self::exportMySqlJson($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function exportMySqlJson(
        \ArrayObject | Dto\ArrayExporterInterface $data
    ) : string 
    {
        $toEncode = ($data instanceof \ArrayObject)
            ? $data->getArrayCopy()
            : $data->toArray();

        return json_encode(array_filter($toEncode));
    }
}