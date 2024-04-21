<?php
namespace Phalconeer\User\Helper;

class UserIdHelper
{
    const DEFAULT_ENCODER_ODD = [
        '3',
        'e',
        '1',
        'o',
        '7',
        'u',
        'a',
        'i',
        '2',
        '4',
    ];

    const DEFAULT_ENCODER_EVEN = [
        'k',
        's',
        'h',
        'z',
        't',
        'v',
        'x',
        'q',
        'r',
        'c',
    ];

    public static function encodeNumber(
        int $number,
        array $oddEncoder = self::DEFAULT_ENCODER_ODD,
        array $evenEncoder = self::DEFAULT_ENCODER_EVEN
    ) : string
    {
        $result = '';
        foreach (str_split($number) as $index => $digit) {
            if ($index % 2) {
                $result .= $oddEncoder[(int) $digit];
            } else {
                $result .= $evenEncoder[(int) $digit];
            }
        }

        return $result;
    }

    public static function generateSafeUserId(
        int $userId,
        int $applicationId = null,
        array $oddEncoder = self::DEFAULT_ENCODER_ODD,
        array $evenEncoder = self::DEFAULT_ENCODER_EVEN
    ) : string
    {
        $applicationEncoded = (is_null($applicationId))
            ? null
            : self::encodeNumber(
                $applicationId,
                $oddEncoder,
                $evenEncoder
            );
        $userEncoded = self::encodeNumber(
            $userId * 111,
            $oddEncoder,
            $evenEncoder
        );

        return implode(
            '',
            array_filter([
                $applicationEncoded,
                $userEncoded
            ])
        );
    }
}