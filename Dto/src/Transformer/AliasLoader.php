<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class AliasLoader implements This\TransformerInterface
{
    const TRAIT_METHOD = 'loadAliases';

    public function __construct(public array $loadAliases)
    {
    }

    public function transform(
        $source,
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
        return self::loadAliasesWithArray(
            $source,
            $this->loadAliases
        );
    }

    public static function loadAliasesWithArray(
        \ArrayObject $inputObject,
        array $aliases
    ) : \ArrayObject 
    {
        if (count($aliases) === 0) {
            return $inputObject;
        }
        foreach ($aliases as $externalProperty => $internalProperty) {
            if ($inputObject->offsetExists($externalProperty)) {
                $inputObject->offsetSet(
                    $internalProperty,
                    $inputObject->offsetGet($externalProperty)
                );
            }
        }
        return $inputObject;
    }
}