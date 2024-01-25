<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    );
}