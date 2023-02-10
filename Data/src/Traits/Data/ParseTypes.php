<?php
namespace Phalconeer\Data\Traits\Data;

use Phalconeer\Data as This;

trait ParseTypes
{
    /**
     * !!!!!!!!!!!!!!!!!!! WARNING !!!!!!!!!!!!!!!!!!!
     * Using DaoMocks with ParseTypes can result in unwanted behaviour, as ParseTypes
     * defines the order of the properties with child being on top.
     * With defined _properties, the child is on the bottom of the property list
     */

    private function parseTypeDoc(string $docComment) : string
    {
        preg_match_all('/@(property|var)+\s*(\\\\)?([A-Za-z0-9\\\]+)(\s)?(.*)?/', $docComment, $matches);

        if (array_key_exists(0,$matches[3])) {
            return $matches[3][0];
        }

        return 'string';
    }

    protected function parseTypes(array $predefinedProperties) : array
    {
        $reflection = new \ReflectionClass(static::class);

        $protectedProperties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $internalProperties = static::getInternalProperties();
        $properties = array_reduce($protectedProperties, function ($aggregator, $property) use ($predefinedProperties, $internalProperties) {
            if ($property->isStatic()
                || in_array($property->name, $internalProperties)) {
                return $aggregator;
            }
            if (array_key_exists($property->name, $predefinedProperties)) {
                $aggregator[$property->name] = $predefinedProperties[$property->name];
            } else {
                $type = $property->getType();
                if (is_null($type)) {
                    $type = This\Property\Any::class;
                }
                if (substr($type, 0, 1) === '?') {
                    $type = substr($type, 1);
                }
                $aggregator[$property->name] = $type;
            }
            return $aggregator;
        }, []);

        return $properties;
    }
}