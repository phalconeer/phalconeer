<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class FilterExporter implements This\TransformerVariableInterface
{
    public function __construct(public array $filter)
    {
    }

    protected static function compareValue($value, $filter) : bool
    {
        if (is_null($filter)) {
            return is_null($value);
        }

        if (is_array($filter)) {
            if (is_array($value)) {
                return $value === $filter;
            }
            return in_array($value, $filter);
        }

        return $value === $filter;
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : \ArrayObject | Data\CommonInterface
    {
        if (!$baseObject instanceof This\ImmutableDtoCollection) {
            return $source;
        }
        
        return self::filterWithArray(
            $source,
            $this->filter
        );
    }

    public static function filterWithArray(
        \ArrayObject | Data\CollectionInterface $source,
        array $filter
    ) : \ArrayObject | Data\CollectionInterface
    {
        $iterator = $source->getIterator();
        $className = get_class($source);
        $filteredCollection = new $className();

        while ($iterator->valid()) {
            $isValid = array_reduce(
                array_keys($filter),
                function ($isValid, $filterKey) use ($iterator, $filter) {
                    return $isValid && self::compareValue(
                        $iterator->current()->{$filterKey}(),
                        $filter[$filterKey]
                    );
                },
                true
            );
            if ($isValid === true) {
                $filteredCollection->offsetSet(null, $iterator->current());
            }
            $iterator->next();
        }

        return $filteredCollection;
    }
}