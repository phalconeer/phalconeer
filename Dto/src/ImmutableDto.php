<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ImmutableDto extends Data\ImmutableData
{
    use This\Traits\ExportTransformer,
        This\Traits\LoadTransformer;

    protected static array $_internalProperties = [
        '_convertChildren',
        '_exportTransformers',
        '_loadTransformers',
        '_preserveKeys',
    ];

    protected static array $_exportTransformers = [];

    protected static array $_loadTransformers = [];

}