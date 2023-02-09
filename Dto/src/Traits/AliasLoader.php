<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait AliasLoader
{
    // public static array $_loadAliases = [];

    /**
     * Recursive loads internal properties crawling up the inheritence tree
     */
    public static function getLoadAliases() : array
    {
        $parentClassName = get_parent_class(static::class);
        $currentLoadAliases = isset(static::$_loadAliases) ? static::$_loadAliases : [];
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge(
                $parentClassName::getLoadAliases(), $currentLoadAliases) : 
            $currentLoadAliases;
    }

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
        if (count($aliases) > 0) {
            foreach ($aliases as $externalProperty => $internalProperty) {
                if ($inputObject->offsetExists($externalProperty)) {
                    $inputObject->offsetSet(
                        $internalProperty,
                        $inputObject->offsetGet($externalProperty)
                    );
                }
            }
        }
        return $inputObject;
    }
}