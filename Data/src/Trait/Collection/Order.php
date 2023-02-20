<?php
namespace Phalconeer\Data\Trait\Collection;

use Phalconeer\Data as This;

trait Order
{
    public function getOrdered($orderBy = 'order', $isAscending = true) : ?self
    {
        $iterator = $this->collection->getIterator();
        $collection = $this->collection;
        $order = [];

        while ($iterator->valid()) {
            $order[$iterator->key()] = $iterator->current()->$orderBy()
                ?? This\Helper\CollectionHelper::COLLECTION_MAX_ITEM_COUNT + count($order);
            $iterator->next();
        }

        if (count($order) === 0) {
            return null;
        }
        ($isAscending) ? asort($order) : arsort($order);
        $ordered = array_map(
            function($key) use ($collection) {
                return $collection->offsetGet($key);
            },
            array_keys($order)
        );

        return new static(new \ArrayObject($ordered));
    }

    public function getSorted(string $sort) : ?self
    {
        $sortPieces = explode(',', $sort);
        $sorted = clone($this);
        while ($currentSort = array_pop($sortPieces)) {
            $isAscending = substr($currentSort, 0, 1) !== '-';
            $sorted = $sorted->getOrdered(
                ($isAscending) ? $currentSort : substr($currentSort, 1),
                $isAscending
            );
        }

        return $sorted;
    }
}