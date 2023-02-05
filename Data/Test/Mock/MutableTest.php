<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data;

class MutableTest extends Data\MutableData
{
    protected static array $_properties = [
        'stringProperty'        => Data\Helper\ParseValueHelper::TYPE_STRING,
        'intProperty'           => Data\Helper\ParseValueHelper::TYPE_INTEGER,
        'floatProperty'         => Data\Helper\ParseValueHelper::TYPE_DOUBLE,
        'boolProperty'          => Data\Helper\ParseValueHelper::TYPE_BOOL,
        'arrayProperty'         => Data\Helper\ParseValueHelper::TYPE_ARRAY,
        'callableProperty'      => Data\Helper\ParseValueHelper::TYPE_CALLABLE,
        'nestedObject'          => MutableTest::class,
        'arrayObject'           => \ArrayObject::class,
        'dateTimeObject'        => \DateTime::class,
    ];

    protected ?string $stringProperty;

    protected ?int $intProperty;

    protected ?float $floatProperty;

    protected ?bool $boolProperty;

    protected ?array $arrayProperty;

    protected $callableProperty;

    protected ?MutableTest $nestedObject;

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

    public function nestedObject() : ?MutableTest
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

    public function setStringProperty(string $stringProperty) : self
    {
        return $this->setValueByKey('stringProperty', $stringProperty);
    }
}