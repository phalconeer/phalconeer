<?php
namespace Phalconeer\User\Transformer;

class NameMask
{
    const TRAIT_METHOD = 'nameMask';

    public static function nameMask(string $source = null) : ?string
    {
        if (empty($source)) {
            return $source;
        }
        return implode(
            '',
            [
                substr($source, 1),
                '***',
                substr($source, -1)
            ]
        );
    }
}