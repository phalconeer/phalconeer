<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Dto;
use Phalconeer\FormValidator as This;

class Form extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\ArrayLoader,
        Dto\Trait\KeysExporter;

    protected static array $fields = [];

    protected string $collectionType = This\Data\FormField::class;

    public static function getFields() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge($parentClassName::getFields(), static::$fields) : 
            static::$fields;
    }

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject
    {
        foreach ($this->getFields() as $field => $rules) {
            if (!$inputObject->offsetExists($field)) {
                $inputObject->offsetSet($field, $rules);
            }
        }
        return $inputObject;
    }
}