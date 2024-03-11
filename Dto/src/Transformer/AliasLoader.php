<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class AliasLoader implements This\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        $aliases = $parameters?->offsetGet('aliases');
        if (is_null($aliases)
            && !is_null($baseObject)
            && $baseObject instanceof This\ImmutableDto) {
            $aliases = $baseObject->getLoadAliases();
        }
        return self::loadAliasesWithArray(
            $source,
            $aliases ?? []
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