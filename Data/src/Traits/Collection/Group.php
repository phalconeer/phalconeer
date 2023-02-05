<?php
namespace Phalconeer\Data\Traits\Collection;

trait Group
{
    public function getGrouped(
        string $groupBy = 'id'
    ) : \ArrayObject
    {
        $iterator = $this->collection->getIterator();
        $grouped = new \ArrayObject();

        while ($iterator->valid()) {
            $groupHandler = $iterator->current()->$groupBy();
            if (!is_array($groupHandler)
                && !is_object($groupHandler)) {
                if (!$grouped->offsetExists($groupHandler)) {
                    $grouped->offsetSet($groupHandler, new static());
                }
                $grouped->offsetGet($groupHandler)->offsetSet(null, $iterator->current());
            }
            $iterator->next();
        }

        return $grouped;
    }
}