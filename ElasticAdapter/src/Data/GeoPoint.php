<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Data;

class GeoPoint extends Data\ImmutableData
{
    use Data\Trait\Data\AutoGetter,
        Data\Trait\Data\ParseTypes;

    protected float $lat;

    protected float $lon;
}