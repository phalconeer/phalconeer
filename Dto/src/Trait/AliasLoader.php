<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait AliasLoader
{
    public function loadAliases(
        \ArrayObject $inputObject
    ) : \ArrayObject 
    {
        return self::loadAliasesWithArray(
            $inputObject,
            $this->getLoadAliases()
        );
    }

    public static function loadAliasesWithArray(
        \ArrayObject $inputObject,
        array $aliases
    ) : \ArrayObject 
    {
        return This\Transformer\AliasLoader::loadAliasesWithArray(
            $inputObject,
            $aliases
        );
    }
}