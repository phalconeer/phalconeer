<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class OrderExporter implements This\TransformerVariableInterface
{
    public function __construct(
        protected string $sortBy = 'order'
    )
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : \ArrayObject
    {
        if (!$baseObject instanceof This\ImmutableDtoCollection) {
            return $source;
        }

        return self::sort(
            $source,
            $this->sortBy
        );

        return $source;
    }

    public static function orderByField(
        \ArrayObject | Data\CollectionInterface $source,
        string $orderBy = 'order',
        bool $isAscending = true
    ) : \ArrayObject | Data\CollectionInterface
    {
        $iterator = $source->getIterator();
        $className = get_class($source);
        $ordered = new $className();
        $order = [];

        while ($iterator->valid()) {
            $order[$iterator->key()] = $iterator->current()->$orderBy()
                ?? Data\Helper\CollectionHelper::COLLECTION_MAX_ITEM_COUNT + count($order);
            $iterator->next();
        }

        if (count($order) === 0) {
            return $ordered;
        }
        ($isAscending) ? asort($order) : arsort($order);
        array_map(
            function($key) use ($source, $ordered) {
                $ordered->offsetSet(null, $source->offsetGet($key));
            },
            array_keys($order)
        );

        return $ordered;
    }

    public static function sort(
        \ArrayObject | Data\CollectionInterface $source,
        string $sortBy
    ) : \ArrayObject | Data\CollectionInterface
    {
        $sortPieces = explode(',', $sortBy);
        $source = clone($source);
        while ($currentSort = array_pop($sortPieces)) {
            $isAscending = substr($currentSort, 0, 1) !== '-';
            $source = self::orderByField(
                $source,
                ($isAscending) ? $currentSort : substr($currentSort, 1),
                $isAscending
            );
        }

        return $source;
    }
}