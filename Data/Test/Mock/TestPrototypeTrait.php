<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Data\Test as This;

class TestPrototypeTrait extends Data\ImmutableData
{
    use Data\Trait\AutoGetter;

    protected static array $properties = [
        'stringProperty'        => Data\Helper\ParseValueHelper::TYPE_STRING,
        'intProperty'           => Data\Helper\ParseValueHelper::TYPE_INTEGER,
        'floatProperty'         => Data\Helper\ParseValueHelper::TYPE_DOUBLE,
        'boolProperty'          => Data\Helper\ParseValueHelper::TYPE_BOOL,
        'arrayProperty'         => Data\Helper\ParseValueHelper::TYPE_ARRAY,
        'callableProperty'      => Data\Helper\ParseValueHelper::TYPE_CALLABLE,
        'nestedObject'          => This\Mock\Test::class,
        'arrayObject'           => \ArrayObject::class,
        'dateTimeObject'        => \DateTime::class,
    ];

    protected ?string $stringProperty;

    protected ?int $intProperty;

    protected ?float $floatProperty;

    protected ?bool $boolProperty;

    protected ?array $arrayProperty;

    protected $callableProperty;

    protected ?This\Mock\Test $nestedObject;

    protected ?\ArrayObject $arrayObject;

    protected ?\DateTime $dateTimeObject;

    protected $undocumented;
}