<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data\Test as This;
use Phalconeer\Data;

class TestParseTypesTrait extends Data\ImmutableData
{
    use Data\Trait\Data\ParseTypes;

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

    public function stringProperty() : ?string
    {
        return $this->getValue('stringProperty');
    }

    public function intProperty() : ?int
    {
        return $this->getValue('intProperty');
    }

    public function floatProperty() : ?float
    {
        return $this->getValue('floatProperty');
    }

    public function boolProperty() : ?bool
    {
        return $this->getValue('boolProperty');
    }

    public function arrayProperty() : ?array
    {
        return $this->getValue('arrayProperty');
    }

    public function callableProperty() : ?callable
    {
        return $this->getValue('callableProperty');
    }

    public function nestedObject() : ?This\Mock\Test
    {
        return $this->getValue('nestedObject');
    }

    public function arrayObject() : ?\ArrayObject
    {
        return $this->getValue('arrayObject');
    }

    public function dateTimeObject() : ?\DateTime
    {
        return $this->getValue('dateTimeObject');
    }

    public function undocumented()
    {
        return $this->getValue('undocumented');
    }
}