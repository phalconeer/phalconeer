<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Data;

class GeoPoint extends Data\ImmutableData
{
    use Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected float $lat;

    protected float $lon;
}