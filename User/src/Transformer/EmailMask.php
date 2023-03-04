<?php
namespace Phalconeer\User\Transformer;

class EmailMask
{
    const TRAIT_METHOD = 'emailMask';

    public static function emailMask(string $source = null) : ?string
    {
        if (empty($source)) {
            return $source;
        }
        $sourcePieces = explode('@', $source);
        return implode(
            '',
            [
                substr($sourcePieces[0], 0, 1),
                strlen($sourcePieces[0]) - 2,
                substr($sourcePieces[0], -1),
                '@',
                $sourcePieces[1]
            ]
        );
    }
}