<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait OrderExporter
{
    public function getOrdered($orderBy = 'order', $isAscending = true) : self
    {
        return This\Transformer\OrderExporter::orderByField(
            $this,
            $orderBy,
            $isAscending
        );
    }

    public function getSorted(string $sort) : self
    {
        return This\Transformer\OrderExporter::sort(
            $this,
            $sort
        );
    }
}