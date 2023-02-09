<?php
namespace Phalconeer\Data\Traits\Collection;

trait Filter
{
    protected function compareValue($value, $filter) : bool
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

    public function filter(array $filter) : self
    {
        $iterator = $this->collection->getIterator();
        $filteredCollection = new \ArrayObject();

        while ($iterator->valid()) {
            $isValid = array_reduce(
                array_keys($filter),
                function ($isValid, $filterKey) use ($iterator, $filter) {
                    return $isValid && $this->compareValue(
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

        return new static($filteredCollection);
    }
}