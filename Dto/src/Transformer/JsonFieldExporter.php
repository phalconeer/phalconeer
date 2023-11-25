<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class JsonFieldExporter implements This\TransformerInterface
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
        if (is_array($source)) {
            $source = new \ArrayObject($source);
        }
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        return self::exportJsonField(
            $source,
            $this->field
        );
    }

    public static function exportJsonField(
        \ArrayObject $source,
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