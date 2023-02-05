<?php
namespace Phalconeer\Id\Helper;

class IdHelper
{
    /**
     * Default length of the conceal ID in binary, it will generate strings doule this length
     */
    const DEFAULT_LENGTH = 24;

    public static function getUuidv4() : string
    {
        return implode('-', [
            bin2hex(random_bytes(4)),
            bin2hex(random_bytes(2)),
            bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
            bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
            bin2hex(random_bytes(6))
        ]);
    }

    
    /**
     * Generates a new unique ID.
     */
    public static function generate(int $length = IdHelper::DEFAULT_LENGTH) : string
    {
        $id = bin2hex(openssl_random_pseudo_bytes($length));
        return substr($id, 0, $length);
    }

    /**
     * Creates a unique ID which has the current date as the first eight characters.
     */
    public static function generateWithDayPrefix(int $length = 0) : string
    {
        return implode('-', [
            (new \DateTime())->format('Ymd'),
            self::generate($length)
        ]);
    }
}
