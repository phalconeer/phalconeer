<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface TransformerInterface
{
    public function transform(
        $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    );
}