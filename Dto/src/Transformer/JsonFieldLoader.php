<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class JsonFieldLoader implements This\TransformerInterface
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
        return self::loadJsonField(
            $source,
            $this->field
        );
    }

    public static function loadJsonField(
        \ArrayObject $inputObject,
        string $field
    ) : \ArrayObject 
    {
        if ($inputObject->offsetExists($field)) {
            $inputObject->offsetSet(
                $field,
                new \ArrayObject(json_decode($inputObject->offsetGet($field), 1))
            );
        }
        return $inputObject;
    }
}