<?php
namespace Phalconeer\Impression\Data;

use Phalconeer\Data;

class ImpressionCollection extends Data\ImmutableCollection
{
    protected $collectionType = Impression::class;
}