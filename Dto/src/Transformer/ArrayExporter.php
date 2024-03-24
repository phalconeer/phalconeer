<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ArrayExporter implements This\TransformerStaticInterface
{
    const TRAIT_METHOD = 'toArray';

    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if ($source instanceof \ArrayObject) {
            return $source->getArrayCopy();
        }
        if (!$baseObject instanceof This\ArrayObjectExporterInterface) {
            return $source;
        }

        return $baseObject->toArrayObject()->getArrayCopy();
    }
}