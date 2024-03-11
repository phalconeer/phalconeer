<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface StaticTransformerInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    );
} 