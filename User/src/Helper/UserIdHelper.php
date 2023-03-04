<?php
namespace Phalconeer\User\Helper;

use Phalconeer\Id;

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

    public static function generateSafeUserId(
        int $userId,
        int $applicationId,
        array $oddEncoder = self::DEFAULT_ENCODER_ODD,
        array $evenEncoder = self::DEFAULT_ENCODER_EVEN) : string
    {
        $index = 0;
        $encodedApplicationId = array_reduce(
            str_split($applicationId),
            function ($aggregator, $currentDigit) use ($oddEncoder, $evenEncoder, &$index) {
                if (++$index % 2) {
                    $aggregator .= $oddEncoder[(int) $currentDigit];
                } else {
                    $aggregator .= $evenEncoder[(int) $currentDigit];
                }
                return $aggregator;
            },
            ''
        );
        $index = 0;
        $encodedUserId = array_reduce(
            str_split($userId * 111),
            function ($aggregator, $currentDigit) use ($oddEncoder, $evenEncoder, &$index) {
                if (++$index % 2) {
                    $aggregator .= $oddEncoder[(int) $currentDigit];
                } else {
                    $aggregator .= $evenEncoder[(int) $currentDigit];
                }
                return $aggregator;
            },
            ''
        );

        return implode(
            '',
            [
                $encodedApplicationId,
                $encodedUserId
            ]
        );
    }

    public static function generateIndependentSafeUserId(string $seed = null)
    {
        if (is_null($seed)) {
            $seed = Id\Helper\IdHelper::generate(12);
        }
        $shortCode = base64_encode(pack('H*', $seed));
        return strtr($shortCode, ['+' => 'fn', '/' => 'tc']);
    }
}