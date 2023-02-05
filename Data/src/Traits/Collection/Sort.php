<?php
namespace Phalconeer\Data\Traits\Collection;

trait Sort
{
    public function getSorted(string $sort) : ?self
    {
        $sortPieces = explode(',', $sort);
        $sorted = $this;
        while ($currentSort = array_pop($sortPieces)) {
            $isAscending = substr($currentSort, 0, 1) !== '-';
            $sorted = $sorted->getOrderedCollection(
                ($isAscending) ? $currentSort : substr($currentSort, 1),
                $isAscending
            );
        }

        return $sorted;
    }
}