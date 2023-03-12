<?php
namespace Phalconeer\Exception\Helper;

use Phalconeer\Dto;
use Phalconeer\Exception as This;

class TraceHelper
{
    const MAX_DEPTH = 2;

    static function flattenExceptionArguments(array $arguments, $depth = 0)
    {
        if ($depth >= self::MAX_DEPTH) {
            return '[...]';
        }
        foreach ($arguments as $key => $value) {
            if (is_object($value)) {
                if ($value instanceof Dto\ArrayExporterInterface) {
                    $arguments[$key] = $value->toArray();
                } elseif ($value instanceof Dto\ArrayObjectExporterInterface) {
                    $arguments[$key] = $value->toArrayObject()->getArrayCopy();
                } else {
                    $arguments[$key] = get_class($value);
                }
            } 
            if (is_array($value)) {
                $arguments[$key] = self::flattenExceptionArguments($value, $depth + 1);
            }
        }

        return $arguments;
    }

    static function getComponent(\Exception $exception)
    {
        return $exception instanceof This\Exception
            ? $exception->getComponent()
            : 'unknown';
    }

    // static function collectSessionVars()
    // {
    //     $sessionKeysToDump = isset(SessionKeyHelper::$dumpOnException)
    //             ? SessionKeyHelper::$dumpOnException
    //             : [];

    //     return array_reduce($sessionKeysToDump, function($result, $sessionKey) {
    //         return array_merge($result, [
    //             $sessionKey => @Di::getDefault()->getSession()->get($sessionKey)
    //         ]);
    //     }, []);
    // }

    static function getServerAddress()
    {
        return array_key_exists('SERVER_ADDR', $_SERVER)
                ? $_SERVER['SERVER_ADDR']
                : 'CLI';
    }

    // static function getRunId()
    // {
    //     if (Phalcon\Di::getDefault()->has('runstat')) {
    //         return Phalcon\Di::getDefault()->get('runstat')->getRunId();
    //     }
    // }
}