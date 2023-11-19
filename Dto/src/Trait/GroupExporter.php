<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait GroupExporter
{
    public function getGrouped(
        string $groupBy = 'id'
    ) : \ArrayObject
    {
        return This\Transformer\GroupExporter::groupByField(
            $this,
            $groupBy
        );
    }
}