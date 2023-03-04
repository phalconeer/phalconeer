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

    public static function rejectComplexValues($value, $expectedType = '')
    {
        if (is_array($value)
                || is_object($value)) {
            throw new Exception\TypeMismatchException(
                $expectedType,
                This\Helper\ExceptionHelper::COMPLEX_VALUE_NOT_ALLOWED
            );
        }
    }

    public static function parseString($value) : ?string
    {
        self::rejectComplexValues($value, self::TYPE_STRING);
        return isset($value)
                ? (string) $value
                : null;
    }

    public static function parseInt($value) : ?int
    {
        self::rejectComplexValues($value, self::TYPE_INT);
        return isset($value)
                ? (int) $value
                : null;
    }

    public static function parseFloat($value) : ?float
    {
        self::rejectComplexValues($value, self::TYPE_FLOAT);
        return isset($value)
                ? (float) $value
                : null;
    }

    public static function parseBool($value) : ?bool
    {
        self::rejectComplexValues($value, self::TYPE_BOOL);
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
        return $value;
    }

    public static function parseCallable($value) : callable
    {
        if (!is_callable($value)) {
            throw new Exception\TypeMismatchException(
                'Expected callable',
                This\Helper\ExceptionHelper::VALUE_IS_NOT_CALLABLE
            );
        }
        return $value;
    }

    public static function parseComplexType($value, $type)
    {
        if (is_null($value)) {
            return null;
        }
        if (is_object($value)
                && is_a($value, $type)) {
            return $value;
        }
        if ($value instanceof \ArrayObject) {
            return new $type($value);
        }

        if (interface_exists($type)) {
            throw new Exception\TypeMismatchException(
                'Expected class implementing interface `' . $type . '`, received: ' . ((is_object($value)) ? get_class($value) : gettype($value)),
                This\Helper\ExceptionHelper::TYPE_MISMATCH
            );
        }

        if ($type === \DateTime::class) {
            try {
                return new \DateTime($value);
            } catch (\Exception $e) {
                return true;
            }
        }
    
        throw new Exception\TypeMismatchException(
            'Expected class `' . $type . '`or ArrayObject, received: ' . (
                (is_object($value))
                ? get_class($value)
                : ((is_array($value))
                    ? 'array with keys: ' . implode(', ', array_keys($value))
                    : $value)
                ),
            This\Helper\ExceptionHelper::TYPE_MISMATCH
        );
    }

    public static function parseValue($value, string $type)
    {
        if (is_null($value)) {
            return null;
        }
        switch ($type) {
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
            case This\TypedProperty::class:
                if((!is_object($value) 
                        || is_a($value, This\TypedProperty::class))
                    && (!is_array($value)
                        || !array_key_exists('value', $value))) {
                    $value = [
                        'value'     => $value,
                        'type'      => self::detectType($value)
                    ];
                }
            default:
                return self::parseComplexType($value, $type);
        }
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
}
