<?php
namespace Phalconeer\Data\Traits\Collection;

trait Map
{
    public function mapFieldAsKey(string $field) : self
    {
        $mappedData = new \ArrayObject();
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $mappedData->offsetSet(
                $iterator->current()->{$field}(),
                $iterator->current()
            );
            $iterator->next();
        }

        return new static($mappedData);
    }
}