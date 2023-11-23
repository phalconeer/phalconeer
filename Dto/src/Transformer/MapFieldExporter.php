<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class MapFieldExporter implements This\TransformerInterface
{
    public function __construct(public string $mapField)
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : \ArrayObject | Data\CollectionInterface
    {
        if (!$baseObject instanceof This\ImmutableDtoCollection) {
            return $source;
        }

        return self::mapFieldAsKey(
            $source,
            $this->mapField
        );
    }

    public static function mapFieldAsKey(
        \ArrayObject | Data\CollectionInterface $source,
        string $field
    ) : \ArrayObject | Data\CollectionInterface
    {
        $iterator = $source->getIterator();
        $className = get_class($source);
        $mappedData = new $className();
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            $currentValue = ($iterator->current() instanceof \ArrayObject)
                ? $iterator->current()->offsetGet($field)
                : $iterator->current()->{$field}();
            $mappedData->offsetSet(
                $currentValue,
                $iterator->current()
            );
            $iterator->next();
        }

        return $mappedData;
    }
}