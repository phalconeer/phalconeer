<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface TransformerVariableInterface
{
    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    );
} 