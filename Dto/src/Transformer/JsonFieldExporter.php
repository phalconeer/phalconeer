<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class JsonFieldExporter implements This\TransformerVariableInterface
{
    public function __construct(public string $field)
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = This\Transformer\ArrayObjectExporter::normalizeArrayObject($source);
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        return self::exportJsonField(
            $source,
            $this->field
        );
    }

    public static function exportJsonField(
        \ArrayObject | Data\CommonInterface $source,
        string $field
    ) : \ArrayObject 
    {
        if ($source->offsetExists($field)) {
            $source->offsetSet(
                $field,
                json_encode($source->offsetGet($field))
            );
        }
        return $source;
    }
}