<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Data;

class GeoPoint extends Data\ImmutableData
{
    use Data\Traits\Data\AutoGetter,
        Data\Traits\Data\ParseTypes;

    protected float $lat;

    protected float $lon;
}