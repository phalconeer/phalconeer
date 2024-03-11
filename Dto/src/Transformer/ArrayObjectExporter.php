<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ArrayObjectExporter
{
    const TRAIT_METHOD = 'toArrayObject';

    public static function normalizeArrayObject(\ArrayObject | Data\CommonInterface | array $source)
    {
        if (is_array($source)) {
            $source = new \ArrayObject($source);
        }
        if (is_object($source)
            && method_exists($source, 'toArrayObject')) {
            $source = $source->toArrayObject();
        }
        if (is_object($source)
            && method_exists($source, 'toArrayObjectWithoutNulls')) {
            $source = $source->toArrayObjectWithoutNulls();
        }

        return $source;
    }
}