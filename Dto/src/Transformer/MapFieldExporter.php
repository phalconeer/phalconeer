<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class MapFieldExporter implements This\TransformerInterface
{
    public function __construct(public string | array $mapField)
    {
        if (!is_array($this->mapField)) {
            $this->mapField = [$this->mapField];
        }
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
        string | array $field
    ) : \ArrayObject | Data\CollectionInterface
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        $iterator = $source->getIterator();
        $className = get_class($source);
        $mappedData = new $className();
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            $current = $iterator->current();
            $currentValuePieces = array_reduce(
                $field,
                function ($aggregator, $currentField) use ($current) {
                    $aggregator[] = ($current instanceof \ArrayObject)
                        ? $current->offsetGet($currentField)
                        : $current->{$currentField}();
                    return $aggregator;
                },
                []
            );
            
            $mappedData->offsetSet(
                implode('-', $currentValuePieces),
                $iterator->current()
            );
            $iterator->next();
        }

        return $mappedData;
    }
}