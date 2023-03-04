<?php
namespace Phalconeer\User\Transformer;

use Phalconeer\User as This;

class SensitiveData
{
    const TRAIT_METHOD = 'exportSensitiveData';

    public static function applyMask(string $mask, $value)
    {
        switch ($mask) {
            case This\Transformer\EmailMask::TRAIT_METHOD:
                return This\Transformer\EmailMask::emailMask($value);
            case This\Transformer\NameMask::TRAIT_METHOD:
                return This\Transformer\NameMask::nameMask($value);
        }
    }

    public static function sensitiveData(
        \ArrayObject $data,
        \ArrayObject $parameters
    ) : \ArrayObject
    {
        if (!$parameters->offsetExists('sensitiveParameters')) {
            return $data;
        }
        $sensitiveParameters = $parameters->offsetGet('masks');
        foreach ($sensitiveParameters as $property => $mask) {
            if ($data->offsetExists($property)) {
                $data->offsetSet(
                    $property,
                    self::applyMask($mask, $data->offsetGet($property))
                );
            }
        }

        return $data;
    }
}