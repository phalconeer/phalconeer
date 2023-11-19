<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait FilterExporter
{
    public function getFiltered(
        array $filter
    ) : \ArrayObject | Data\CommonInterface
    {
        return This\Transformer\FilterExporter::filterWithArray(
            $this,
            $filter
        );
    }
}