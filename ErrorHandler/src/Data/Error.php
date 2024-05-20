<?php
namespace Phalconeer\ErrorHandler\Data;

use Phalconeer\ErrorHandler as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class Error extends Dto\ImmutableDto
{
    use This\Trait\ErrorNumber,
        This\Trait\File,
        This\Trait\Globals,
        This\Trait\Id,
        This\Trait\Line,
        This\Trait\Message,
        This\Trait\RequestTime,
        This\Trait\Server,
        This\Trait\Trace,
        Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected static array $loadTransformers = [
        This\Transformer\AutoFillRequestTime::class,
    ];
}
