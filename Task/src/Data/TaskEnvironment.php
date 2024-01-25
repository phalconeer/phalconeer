<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class TaskEnvironment extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayObjectExporter,
        Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected ?string $productName;

    protected ?string $server;
}