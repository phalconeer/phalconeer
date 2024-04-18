<?php
namespace Phalconeer\User\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class NameMask implements Dto\TransformerInterface
{
    public static $defaultNameFields = [
        'username',
    ];

    public function __construct(
        protected ?array $nameFields = null
    )
    {
        if (is_null($this->nameFields)) {
            $this->nameFields = static::$defaultNameFields;
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
        foreach ($this->nameFields as $emailField) {
            if (!$source->offsetExists($emailField)) {
                $source->offsetSet($emailField, self::nameMask($source->offsetGet($emailField)));
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
        foreach (static::$defaultNameFields as $emailField) {
            if (!$source->offsetExists($emailField)) {
                $source->offsetSet($emailField, self::nameMask($source->offsetGet($emailField)));
            }
        }
        return $source;
    }

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