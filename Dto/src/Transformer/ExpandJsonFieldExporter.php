<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ExpandJsonFieldExporter implements This\TransformerInterface
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
        return self::exportExpandedJsonField(
            $source,
            $this->field
        );
    }

    public static function exportExpandedJsonField(
        \ArrayObject | Data\CommonInterface $source,
        string $field
    ) : \ArrayObject 
    {
        if ($source->offsetExists($field)) {
            $current = $source->offsetGet($field);
            if ($current instanceof  Data\DataInterface) {
                $properties = $current->properties();
                foreach ($properties as $property) {
                    if (!$source->offsetExists($property)) {
                        $source->offsetSet(
                            $property,
                            $current->{$property}()
                        );
                    }
                }
            }
            $source->offsetUnset($field);
        }
        return $source;
    }
}