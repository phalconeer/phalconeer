<?php
namespace Phalconeer\User\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class EmailMask implements Dto\TransformerInterface
{
    public static $defaultEmailFields = [
        'email'
    ];

    public function __construct(
        protected ?array $emailFields = null
    )
    {
        if (is_null($this->emailFields)) {
            $this->emailFields = static::$defaultEmailFields;
        }   
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source = null,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : ?\ArrayObject
    {
        if (is_null($source)) {
            return $source;
        }
        foreach ($this->emailFields as $emailField) {
            if (!$source->offsetExists($emailField)) {
                $source->offsetSet($emailField, self::emailMask($source->offsetGet($emailField)));
            }
        }
        return $source;
    }

    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source = null,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : ?\ArrayObject
    {
        if (is_null($source)) {
            return $source;
        }
        foreach (static::$defaultEmailFields as $emailField) {
            if (!$source->offsetExists($emailField)) {
                $source->offsetSet($emailField, self::emailMask($source->offsetGet($emailField)));
            }
        }
        return $source;
    }

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