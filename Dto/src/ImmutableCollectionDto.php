<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ImmutableCollectionDto extends Data\ImmutableCollection
{
    use This\Traits\ExportTransformer,
        This\Traits\LoadTransformer;

    protected static array $_internalProperties = [
        '_convertChildren',
        '_exportTransformers',
        '_loadTransformers',
        '_preserveKeys',
    ];
}