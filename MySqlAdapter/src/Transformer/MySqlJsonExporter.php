<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class MySqlJsonExporter implements Dto\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllMySqlJson';

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
            $parameters->offsetSet('jsonProperties', MySqlJsonLoader::getJsonProperties($baseObject));
        }
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
                && $iterator->current() instanceof Dto\ImmutableDto) {
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
        Dto\ArrayExporterInterface $data
    ) : string 
    {
        return json_encode($data->toArray());
    }
}