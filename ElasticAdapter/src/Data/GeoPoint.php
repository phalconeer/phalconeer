<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class GeoPoint extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayExporter,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayExporter::TRAIT_METHOD,
    ];

    protected float $lat;

    protected float $lon;
}