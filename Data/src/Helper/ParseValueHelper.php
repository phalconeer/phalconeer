<?php
namespace Phalconeer\Data\Helper;

use Phalconeer\Data as This;
use Phalconeer\Exception;

class ParseValueHelper
{
    const TYPE_STRING = 'string';

    const TYPE_INT = 'int';

    const TYPE_INTEGER = 'integer';

    const TYPE_FLOAT = 'float';

    const TYPE_DOUBLE = 'double';

    const TYPE_REAL = 'real';

    const TYPE_BOOL = 'bool';

    const TYPE_BOOLEAN = 'boolean';

    const TYPE_ARRAY = 'array';

    const TYPE_CALLABLE = 'callable';

    const TYPE_REFERENCE = 'reference';

    public static function isSimpleValue($type)
    {
        return in_array($type, [
            self::TYPE_STRING,
            self::TYPE_INTEGER,
            self::TYPE_INT,
            self::TYPE_DOUBLE,
            self::TYPE_FLOAT,
            self::TYPE_REAL,
            self::TYPE_BOOLEAN,
            self::TYPE_BOOL,
            self::TYPE_ARRAY,
            self::TYPE_CALLABLE,
        ]);
    }

    public static function validateSimpleValue($value) : ?int
    {
        if (!is_array($value)
                && !is_object($value)) {
            return null;
        }
        return This\Helper\ExceptionHelper::COMPLEX_VALUE_NOT_ALLOWED;
    }

    public static function validateCallable($value) : ?int
    {
        if (is_callable($value)) {
            return null;
        }
        return This\Helper\ExceptionHelper::VALUE_IS_NOT_CALLABLE;
    }

    public static function validateTyped($value) : ?int
    {
        if((is_object($value) 
                && is_a($value, This\Property\Typed::class))
            || (is_array($value)
                && array_key_exists('value', $value))
            || ($value instanceof \ArrayObject)
                && $value->offsetExists('value')) {
            return null;
        }
        return self::validateSimpleValue($value);
    }

    public static function validateComplexType($value, $type) : ?int
    {
        if (is_object($value)
                && is_a($value, $type)) {
            return null;
        }
        if ($value instanceof \ArrayObject) {
            return null;
        }

        if ($type === \DateTime::class) {
            try {
                new \DateTime($value);
            } catch (\Exception $e) {
                return This\Helper\ExceptionHelper::INVALID_DATE;
            }
            return null;
        }

        if (interface_exists($type)) {
            return This\Helper\ExceptionHelper::TYPE_MISMATCH_INTERFACE;
        }
    
        return This\Helper\ExceptionHelper::TYPE_MISMATCH;
    }

    public static function parseString($value) : ?string
    {
        return isset($value)
                ? (string) $value
                : null;
    }

    public static function parseInt($value) : ?int
    {
        return isset($value)
                ? (int) $value
                : null;
    }

    public static function parseFloat($value) : ?float
    {
        return isset($value)
                ? (float) $value
                : null;
    }

    public static function parseBool($value) : ?bool
    {
        if (is_bool($value)) {
            return $value;
        }
        else {
            return isset($value)
                    ? $value === 'true'
                    : null;
        }
    }

    public static function parseArray($value) : array
    {
        if ($value instanceof \ArrayObject) {
            return $value->getArrayCopy();
        }
        if (!is_array($value)) {
            return explode(',', $value);
        }
        return $value;
    }

    public static function parseArrayObject($value) : \ArrayObject
    {
        if ($value instanceof \ArrayObject) {
            return $value;
        }
        if (is_array($value)) {
            return new \ArrayObject($value);
        }
        return new \ArrayObject(explode(',', $value));
    }

    public static function parseCallable($value) : callable
    {
        return $value;
    }

    public static function parseTyped($value)
    {
        if(is_object($value) 
                && is_a($value, This\Property\Typed::class)) {
            return $value;
        }
        if (is_array($value)
                && array_key_exists('value', $value)) {
            $value = new \ArrayObject($value);
        }
        if (!$value->offsetExists('type')) {
            $value->offsetSet(
                'type',
                self::detectType($value->offsetGet($value))
            );
        }
        return $value;
    }

    public static function parseComplexType($value, $type)
    {
        if (is_object($value)
                && is_a($value, $type)) {
            return $value;
        }
        if ($value instanceof \ArrayObject) {
            return new $type($value);
        }

        if ($type === \DateTime::class) {
            return new \DateTime($value);
        }

        return null;
    }

    public static function detectType($value, $convertNumeric = true) : string
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            return self::TYPE_ARRAY;
        }

        if (is_bool($value)) {
            return self::TYPE_BOOL;
        }

        if (!$convertNumeric
            && is_string($value)) {
            return self::TYPE_STRING;
        }

        if (is_numeric($value)) {
            $value = $value + 0;
            if (is_double($value)) {
                return self::TYPE_DOUBLE;
            }

            if (is_float($value)) {
                return self::TYPE_FLOAT;
            }

            if (is_int($value)) {
                return self::TYPE_INT;
            }
        }

        return self::TYPE_STRING;
    }

    public static function validateValue(
        $value,
        string $type,
    ) : ?int
    {
        if (is_null($value)) {
            return null;
        }
        switch ($type) {
            case This\Property\Any::class:
                return null;
            case self::TYPE_STRING:
            case self::TYPE_INTEGER: //return value for gettype()
            case self::TYPE_INT:
            case self::TYPE_DOUBLE: //return value for gettype()
            case self::TYPE_FLOAT:
            case self::TYPE_REAL:
            case self::TYPE_BOOLEAN: //return value for gettype()
            case self::TYPE_BOOL:
                return self::validateSimpleValue($value);
            case self::TYPE_ARRAY:
            case \ArrayObject::class:
                return null;
            case self::TYPE_CALLABLE:
                return self::validateCallable($value);
            case This\Property\Typed::class:
                return self::validateTyped($value);
            default:
                return self::validateComplexType($value, $type);
        }
    }

    public static function getValidatedType(
        $value,
        string | array $type,
    ) : string
    {
        $validationError = null;
        if (!is_array($type)) {
            $validationError = self::validateValue($value, $type);
            $validatedType = $type;
        } else {
            $validationError = array_reduce(
                $type,
                function ($aggregate, $currentType) use ($value, &$validatedType) {
                    if (is_null($aggregate)) {
                        return $aggregate; //If one type matches, it is valid
                    }
                    if (is_null(self::validateValue($value, $currentType))) {
                        $validatedType = $currentType;
                        return null;
                    }
                    return $aggregate;
                },
                This\Helper\ExceptionHelper::ALL_TYPE_VALIDATIONS_FAILED
            );
        }


        if (!is_null($validationError)) {
            self::handleValidationError(
                $validationError,
                $value,
                $type
            );
        }

        return $validatedType;
    }

    public static function parseValue(
        $value,
        string | array $type,
    )
    {
        if (is_null($value)) {
            return null;
        }

        $validatedType = self::getValidatedType($value, $type);
        switch ($validatedType) {
            case This\Property\Any::class:
                return $value;
            case self::TYPE_STRING:
                return self::parseString($value);
            case self::TYPE_INTEGER: //return value for gettype()
            case self::TYPE_INT:
                return self::parseInt($value);
            case self::TYPE_DOUBLE: //return value for gettype()
            case self::TYPE_FLOAT:
            case self::TYPE_REAL:
                return self::parseFloat($value);
            case self::TYPE_BOOLEAN: //return value for gettype()
            case self::TYPE_BOOL:
                return self::parseBool($value);
            case self::TYPE_ARRAY:
                return self::parseArray($value);
            case self::TYPE_CALLABLE:
                return self::parseCallable($value);
            case \ArrayObject::class:
                return self::parseArrayObject($value);
            case This\Property\Typed::class:
                return self::parseTyped($value);
            default:
                return self::parseComplexType($value, $validatedType);
        }
    }

    public static function handleValidationError(
        int $error,
        $value,
        string | array $type
    )
    {
        switch ($error) {
            case (This\Helper\ExceptionHelper::ALL_TYPE_VALIDATIONS_FAILED):
                $message = 'All valiadtions failed, types allowed: `' . implode(', ', $type) . '`. Value received: ' . var_export($value, 1);
                break;
            case (This\Helper\ExceptionHelper::COMPLEX_VALUE_NOT_ALLOWED):
                $message = 'Complex value not allowed for `' . $type . '`';
                break;
            case (This\Helper\ExceptionHelper::INVALID_DATE):
                $message = 'Unable to convert to date `' . var_export($value, 1) . '`';
                break;
            case (This\Helper\ExceptionHelper::TYPE_MISMATCH):
                $message = 'Expected class `' . $type . '` or ArrayObject, received: ' . var_export($value, 1);
                break;
            case (This\Helper\ExceptionHelper::TYPE_MISMATCH_INTERFACE):
                $message = 'Expected class to implement interfaec `' . $type . '`, received: ' . var_export($value, 1);
                break;
            case (This\Helper\ExceptionHelper::VALUE_IS_NOT_CALLABLE):
                $message = 'Not callable';
                break;
        }

        throw new Exception\TypeMismatchException($message, $error);
    }

    public static function getBoolProperties(This\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                if (!is_array($type)) {
                    $type = [$type];
                }
                return in_array(This\Helper\ParseValueHelper::TYPE_BOOL, $type)
                    || in_array(This\Helper\ParseValueHelper::TYPE_BOOLEAN, $type);
            }
        );
    }

    public static function getDateProperties(This\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                if (!is_array($type)) {
                    $type = [$type];
                }
                return in_array(\DateTime::class, $type);
            }
        );
    }

    public static function getNestedProperties(This\CommonInterface $baseObject)
    {
        return array_filter(
            $baseObject->propertyTypes(),
            function ($type) {
                if (!is_array($type)) {
                    $type = [$type];
                }
                return array_reduce(
                    $type,
                    function (bool $aggregator, $currentType) {
                        return $aggregator || is_subclass_of($currentType, This\ImmutableData::class);
                    },
                    false
                );
            }
        );
    }
}
