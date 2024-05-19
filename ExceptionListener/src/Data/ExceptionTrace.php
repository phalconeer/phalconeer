<?php
namespace Phalconeer\ExceptionListener\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class ExceptionTrace extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayObjectExporter,
        Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected ?array $arguments;

    protected string $class;
    
    protected ?string $file;
    
    protected string $function;

    protected ?int $line;
    
    protected string $type;
}