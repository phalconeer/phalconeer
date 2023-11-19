<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface TransformerInterface
{
    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    );
}